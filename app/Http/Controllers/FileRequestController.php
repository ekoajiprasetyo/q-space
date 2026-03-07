<?php

namespace App\Http\Controllers;

use App\Models\FileRequest;
use App\Models\FileSubmission;
use App\Models\UserGoogleToken;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileRequestController extends Controller
{
    protected GoogleDriveService $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Check if teacher has connected Google Drive
        $googleToken = UserGoogleToken::where('user_id', $user->id)->first();

        $query = FileRequest::where('teacher_id', $user->id);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter Status
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $fileRequests = $query->withCount(['submissions as submissions_count' => function ($query) {
            $query->select(\Illuminate\Support\Facades\DB::raw('count(distinct submitter_name)'));
        }])->latest()->paginate(10);

        // We are reusing the dashboard.teacher view because it IS the Files Dashboard view
        return view('file-requests.index', compact('fileRequests', 'googleToken'));
    }

    public function create()
    {
        return view('file-requests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_files' => 'required|integer|min:1',
            'deadline' => 'nullable|date|after:now',
        ]);

        $user = Auth::user();

        // 1. Get Teacher's Google Token
        $token = UserGoogleToken::where('user_id', $user->id)->first();

        if (!$token) {
            return redirect()->route('dashboard')->with('error', 'Please connect your Google Drive first.');
        }

        try {
            // 2. Initialize Service with Token
            $this->googleDriveService->setAccessToken($token);

            // 3. Create Folder in Drive
            // First, check/create the main "Files in Q-Space" folder
            $rootFolderName = 'Files in Q-Space';
            $rootFolderId = $this->googleDriveService->findFolderByName($rootFolderName);

            if (!$rootFolderId) {
                $rootFolderId = $this->googleDriveService->createFolder($rootFolderName);
            }

            // Create specific request folder inside the root folder
            // Using just title as requested: "Tugas Video"
            // We might want to append date if uniqueness is an issue, but user asked for "Tugas Video"
            $folderName = $request->title; 
            
            // Check if folder exists inside root (to avoid duplicates if they reuse title?)
            // For now, let's just create it. Google Drive allows duplicate names.
            $driveFolderId = $this->googleDriveService->createFolder($folderName, $rootFolderId);

            // 4. Save to Database
            FileRequest::create([
                'teacher_id' => $user->id,
                'title' => $request->title,
                'slug' => Str::slug($request->title) . '-' . Str::random(6),
                'description' => $request->description,
                'deadline' => $request->deadline,
                'google_drive_folder_id' => $driveFolderId,
                'is_active' => true,
                'allowed_extensions' => ['pdf', 'doc', 'docx', 'jpg', 'lpng', 'zip'], // Default for now
                'max_file_size' => 10, // Default 10MB
                'max_files' => $request->max_files ?? 1,
            ]);

            return redirect()->route('files.index')->with('success', 'File Request created and Drive folder ready!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create Drive folder: ' . $e->getMessage());
        }
    }

    public function destroy(FileRequest $fileRequest)
    {
        if ((int)$fileRequest->teacher_id !== (int)Auth::id()) {
            abort(403);
        }

        // Check if empty
        if ($fileRequest->submissions()->count() > 0) {
            return back()->with('error', 'Gagal menghapus! Folder tidak kosong (sudah ada yang mengumpulkan file).');
        }

        // Get Token to delete from Drive
        $token = UserGoogleToken::where('user_id', Auth::id())
            ->where('expires_at', '>', now())
            ->first();

        try {
            if ($token && $fileRequest->google_drive_folder_id) {
                $this->googleDriveService->setAccessToken($token);
                $this->googleDriveService->deleteFile($fileRequest->google_drive_folder_id);
            }
            
            $fileRequest->delete();
            return back()->with('success', 'Folder dan permintaan file berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus folder Drive: ' . $e->getMessage());
        }
    }

    public function destroySubmission(FileRequest $fileRequest, Request $request)
    {
        if ((int)$fileRequest->teacher_id !== (int)Auth::id()) {
            abort(403);
        }

        $request->validate([
            'submitter_name' => 'required|string'
        ]);

        $submissions = $fileRequest->submissions()
            ->where('submitter_name', $request->submitter_name)
            ->get();

        if ($submissions->isEmpty()) {
            return back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get Token — tidak filter expires_at agar bisa auto-refresh
        $token = UserGoogleToken::where('user_id', Auth::id())->first();

        // Initialize Drive Service if we have token
        if ($token) {
            $this->googleDriveService->setAccessToken($token);
        }

        $deletedCount = 0;
        foreach ($submissions as $submission) {
            try {
                // Delete individual file from Drive if ID exists
                if ($token && $submission->google_drive_file_id) {
                    $this->googleDriveService->deleteFile($submission->google_drive_file_id);
                }
            } catch (\Exception $e) {
                // Continue deleting DB record even if Drive delete fails (orphaned files)
            }
            
            $submission->delete();
            $deletedCount++;
        }

        // Rekonstruksi nama folder siswa dan hapus dari Google Drive
        // Format nama folder: {class_name}_{name} — diambil dari submitter_name yang formatnya "Nama (Kelas)"
        if ($token && $fileRequest->google_drive_folder_id) {
            try {
                // submitter_name disimpan dalam format: "Nama (Kelas)"
                // Rekonstruksi folder name: "Kelas_Nama" (tanpa karakter khusus)
                $rawName = $request->submitter_name;
                // Ekstrak nama dan kelas dari format "Nama (Kelas)"
                if (preg_match('/^(.+)\s+\((.+)\)$/', $rawName, $matches)) {
                    $studentName  = trim($matches[1]);
                    $studentClass = trim($matches[2]);
                    $rawFolderName    = $studentClass . '_' . $studentName;
                    $studentFolderName = preg_replace('/[^A-Za-z0-9 _\-]/', '', $rawFolderName);
                    $studentFolderName = trim($studentFolderName);

                    if ($studentFolderName) {
                        $studentFolderId = $this->googleDriveService->findFolderByName(
                            $studentFolderName,
                            $fileRequest->google_drive_folder_id
                        );

                        if ($studentFolderId) {
                            $this->googleDriveService->deleteFile($studentFolderId);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Silent fail — folder sudah mau dihapus isinya, kalau folder-nya gagal hapus tidak masalah
            }
        }

        return back()->with('success', "Berhasil menghapus $deletedCount file milik {$request->submitter_name}.");
    }

    public function toggleStatus(FileRequest $fileRequest)
    {
        // Ensure user owns this request
        if ((int)$fileRequest->teacher_id !== (int)Auth::id()) {
            abort(403);
        }

        $fileRequest->is_active = !$fileRequest->is_active;
        $fileRequest->save();

        return response()->json([
            'success' => true,
            'is_active' => $fileRequest->is_active,
            'message' => $fileRequest->is_active ? 'Permintaan file diaktifkan.' : 'Permintaan file dinonaktifkan.'
        ]);
    }

    public function show(FileRequest $fileRequest, Request $request)
    {
        if ((int)$fileRequest->teacher_id !== (int)Auth::id()) {
            abort(403);
        }

        // 1. Base Query
        $query = $fileRequest->submissions();

        // 2. Search
        if ($request->has('search') && $request->search) {
             $query->where('submitter_name', 'like', '%' . $request->search . '%');
        }

        // 3. Paginate Distinct Students
        $distinctSubmitters = $query->clone()
            ->select('submitter_name', \Illuminate\Support\Facades\DB::raw('MAX(submitted_at) as last_submitted'))
            ->groupBy('submitter_name')
            ->orderBy('last_submitted', 'desc')
            ->paginate(12)
            ->withQueryString();

        // 4. Fetch Submissions for current page students
        $submitterNames = $distinctSubmitters->pluck('submitter_name')->toArray();

        $submissions = $fileRequest->submissions()
             ->whereIn('submitter_name', $submitterNames)
             ->latest()
             ->get()
             ->groupBy('submitter_name');

        return view('file-requests.show', compact('fileRequest', 'submissions', 'distinctSubmitters'));
    }

    public function publicUpload($slug)
    {
        $fileRequest = FileRequest::where('slug', $slug)->firstOrFail();

        if (!$fileRequest->is_active) {
            abort(404, 'File request is no longer active.');
        }

        // Ideally check deadline too
        if ($fileRequest->deadline && now()->gt($fileRequest->deadline)) {
             // For now we just show it but maybe disable upload form in view
             // or abort. Let's just pass it to view.
        }

        return view('file-requests.upload', compact('fileRequest'));
    }

    public function storePublicUpload(Request $request, $slug)
    {
        $fileRequest = FileRequest::where('slug', $slug)->firstOrFail();

        // Check if active
        if (!$fileRequest->is_active) {
            return redirect()->route('file-requests.upload', $slug)
                ->with('error', 'Permintaan file ini sudah tidak aktif.');
        }
        
        // Check Deadline
        if ($fileRequest->deadline && now()->gt($fileRequest->deadline)) {
            return redirect()->route('file-requests.upload', $slug)
                ->with('error', 'Batas waktu pengumpulan telah berakhir.');
        }

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'class_name' => 'required|string|max:255',
            'notes'      => 'nullable|string|max:1000',
            'files'      => 'required|array|min:1',
            'files.*'    => 'required', // Tanpa max — batas diurus PHP & Google Drive
        ]);

        // Ambil token guru — tidak filter expires_at agar bisa auto-refresh
        $token = UserGoogleToken::where('user_id', $fileRequest->teacher_id)->first();

        if (!$token) {
            return redirect()->route('file-requests.upload', $slug)
                ->with('error', 'Guru belum menghubungkan Google Drive. Hubungi pemilik link ini.');
        }

        try {
            $this->googleDriveService->setAccessToken($token);

            $files = $request->file('files');
            
            // Buat nama folder siswa: {Kelas}_{Nama} (hanya karakter aman)
            $rawFolderName    = $request->class_name . '_' . $request->name;
            $studentFolderName = preg_replace('/[^A-Za-z0-9 _\-]/', '', $rawFolderName);
            $studentFolderName = trim($studentFolderName) ?: 'Siswa_' . now()->timestamp;
            
            // Cari folder siswa yang sudah ada, buat jika belum
            $studentFolderId = $this->googleDriveService->findFolderByName(
                $studentFolderName,
                $fileRequest->google_drive_folder_id
            );

            if (!$studentFolderId) {
                $studentFolderId = $this->googleDriveService->createFolder(
                    $studentFolderName,
                    $fileRequest->google_drive_folder_id
                );
            }

            $uploadedFileNames = [];
            foreach ($files as $file) {
                // Simpan ukuran & mime SEBELUM upload karena uploadFile()
                // menghapus file temp dari disk setelah selesai upload ke Drive
                $fileSize  = $file->getSize();
                $mimeType  = $file->getMimeType() ?: 'application/octet-stream';
                $origName  = $file->getClientOriginalName();

                $driveFile = $this->googleDriveService->uploadFile(
                    $file,
                    $studentFolderId,
                    $origName
                );

                FileSubmission::create([
                    'file_request_id'     => $fileRequest->id,
                    'user_id'             => null,
                    'submitter_name'      => $request->name . ' (' . $request->class_name . ')',
                    'original_filename'   => $origName,
                    'google_drive_file_id'=> $driveFile['id'],
                    'google_drive_url'    => $driveFile['url'],
                    'file_size'           => $fileSize,
                    'mime_type'           => $mimeType,
                    'submitted_at'        => now(),
                    'student_notes'       => $request->notes,
                ]);

                $uploadedFileNames[] = $origName;
            }

            // Gunakan redirect()->route() (bukan back()) agar session flash terbawa dengan benar
            return redirect()->route('file-requests.upload', $slug)
                ->with('submission_details', [
                    'name'  => $request->name,
                    'class' => $request->class_name,
                    'notes' => $request->notes,
                    'files' => $uploadedFileNames,
                ]);

        } catch (\Exception $e) {
            return redirect()->route('file-requests.upload', $slug)
                ->with('error', 'Gagal mengupload file: ' . $e->getMessage())
                ->withInput();
        }
    }
}
