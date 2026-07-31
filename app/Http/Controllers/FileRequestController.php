<?php

namespace App\Http\Controllers;

use App\Jobs\UploadSubmissionToDriveJob;
use App\Models\FileRequest;
use App\Models\FileSubmission;
use App\Models\UploadTask;
use App\Models\UserGoogleToken;
use App\Services\GoogleDriveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
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
        $googleToken = UserGoogleToken::ownedByIdentity((int) $user->id)->first();

        $query = FileRequest::ownedByTeacherIdentity((int) $user->id);

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
            'max_file_size' => 'required|integer|min:1|max:1024',
            'deadline' => 'nullable|date|after:now',
        ]);

        $user = Auth::user();

        // 1. Get Teacher's Google Token
        $token = UserGoogleToken::ownedByIdentity((int) $user->id)->first();

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
                ...FileRequest::ownerAttributes((int) $user->id),
                'title' => $request->title,
                'slug' => Str::slug($request->title) . '-' . Str::random(6),
                'description' => $request->description,
                'deadline' => $request->deadline,
                'google_drive_folder_id' => $driveFolderId,
                'is_active' => true,
                'allowed_extensions' => null,
                'max_file_size' => (int) $request->max_file_size,
                'max_files' => $request->max_files ?? 1,
            ]);

            return redirect()->route('files.index')->with('success', 'File Request created and Drive folder ready!');

        } catch (\Exception $e) {
            Log::warning('Failed to create file request Drive folder', [
                'user_id' => $user->id,
                'google_token_id' => $token->id ?? null,
                'error' => $e->getMessage(),
            ]);

            $message = str_contains(strtolower($e->getMessage()), 'insufficient')
                ? 'Google Drive terhubung, tetapi izinnya belum cukup. Silakan klik "Ganti Akun" atau hubungkan ulang Google Drive untuk memperbarui izin akses.'
                : 'Failed to create Drive folder: ' . $e->getMessage();

            return back()->with('error', $message)->withInput();
        }
    }

    public function destroy(FileRequest $fileRequest)
    {
        if (! $fileRequest->ownerMatches((int) Auth::id())) {
            abort(403);
        }

        // Check if empty
        if ($fileRequest->submissions()->count() > 0) {
            return back()->with('error', 'Gagal menghapus! Folder tidak kosong (sudah ada yang mengumpulkan file).');
        }

        // Get Token to delete from Drive
        $token = UserGoogleToken::ownedByIdentity((int) Auth::id())
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
        if (! $fileRequest->ownerMatches((int) Auth::id())) {
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
        $token = UserGoogleToken::ownedByIdentity((int) Auth::id())->first();

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
        if (! $fileRequest->ownerMatches((int) Auth::id())) {
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
        if (! $fileRequest->ownerMatches((int) Auth::id())) {
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
        
        $uploadTasks = UploadTask::where('file_request_id', $fileRequest->id)
            ->whereIn('submitter_name', $submitterNames)
            ->latest()
            ->get()
            ->groupBy('submitter_name');

        $uploadTaskSummary = UploadTask::where('file_request_id', $fileRequest->id)
            ->selectRaw("SUM(CASE WHEN status IN ('queued','processing') THEN 1 ELSE 0 END) as pending_count")
            ->selectRaw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count")
            ->first();

        $orphanUploadTasks = UploadTask::where('file_request_id', $fileRequest->id)
            ->whereIn('status', ['queued', 'processing', 'failed'])
            ->whereNotIn('submitter_name', $submitterNames)
            ->latest()
            ->get();

        $queueRunnerUrl = URL::temporarySignedRoute(
            'queue.trigger',
            now()->addMinutes(30),
            ['runs' => 1]
        );

        return view('file-requests.show', compact(
            'fileRequest',
            'submissions',
            'distinctSubmitters',
            'uploadTasks',
            'uploadTaskSummary',
            'orphanUploadTasks',
            'queueRunnerUrl'
        ));
    }

    public function uploadStatus(FileRequest $fileRequest): JsonResponse
    {
        if (! $fileRequest->ownerMatches((int) Auth::id())) {
            abort(403);
        }

        $tasks = UploadTask::where('file_request_id', $fileRequest->id)
            ->whereIn('status', ['queued', 'processing', 'uploaded', 'failed'])
            ->get(['id', 'status', 'uploaded_bytes', 'file_size']);

        $pendingCount = $tasks->whereIn('status', ['queued', 'processing'])->count();

        return response()->json([
            'pending_count' => $pendingCount,
            'tasks' => $tasks->mapWithKeys(function (UploadTask $task): array {
                $totalBytes = max(1, (int) $task->file_size);
                $uploadedBytes = max(0, min((int) $task->uploaded_bytes, $totalBytes));

                return [(string) $task->id => [
                    'status' => $task->status,
                    'uploaded_bytes' => $uploadedBytes,
                    'file_size' => $totalBytes,
                    'progress_percent' => (int) floor(($uploadedBytes / $totalBytes) * 100),
                ]];
            }),
        ]);
    }

    public function retryUploadTask(FileRequest $fileRequest, UploadTask $uploadTask)
    {
        if (! $fileRequest->ownerMatches((int) Auth::id())) {
            abort(403);
        }

        if ((int)$uploadTask->file_request_id !== (int)$fileRequest->id) {
            abort(404);
        }

        if (!in_array($uploadTask->status, ['failed', 'queued'], true)) {
            return back()->with('error', 'Task ini tidak bisa di-retry.');
        }

        if (!\Storage::disk('local')->exists($uploadTask->staged_path)) {
            return back()->with('error', 'File staging untuk retry tidak ditemukan di server.');
        }

        $uploadTask->update([
            'status' => 'queued',
            'queued_at' => now(),
            'processed_at' => null,
            'last_error' => null,
        ]);

        UploadSubmissionToDriveJob::dispatch($uploadTask->id)->onQueue('uploads');

        return back()->with('success', 'Retry upload dijadwalkan. Refresh halaman beberapa saat lagi.');
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

    public function createChunkBatch(Request $request, string $slug): JsonResponse
    {
        $fileRequest = $this->resolvePublicFileRequest($slug);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'file_count' => 'required|integer|min:1|max:' . max(1, (int) $fileRequest->max_files),
        ]);

        if (!UserGoogleToken::ownedByIdentity($fileRequest->ownerIdentityId())->exists()) {
            return response()->json([
                'message' => 'Guru belum menghubungkan Google Drive. Hubungi pemilik link ini.',
            ], 422);
        }

        $batchId = (string) Str::uuid();
        $batchDirectory = $this->chunkBatchDirectory($fileRequest, $batchId);

        Storage::disk('local')->makeDirectory($batchDirectory);
        Storage::disk('local')->put($this->chunkBatchMetadataPath($fileRequest, $batchId), json_encode([
            'batch_id' => $batchId,
            'file_request_id' => $fileRequest->id,
            'teacher_identity_id' => $fileRequest->ownerIdentityId(),
            'name' => $validated['name'],
            'class_name' => $validated['class_name'],
            'notes' => $validated['notes'] ?? null,
            'file_count' => (int) $validated['file_count'],
            'uploaded_files' => [],
            'created_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return response()->json([
            'batch_id' => $batchId,
            'chunk_size' => $this->browserUploadChunkSize(),
        ]);
    }

    public function createChunkFileUpload(Request $request, string $slug): JsonResponse
    {
        $fileRequest = $this->resolvePublicFileRequest($slug);

        $validated = $request->validate([
            'batch_id' => 'required|string',
            'file_name' => 'required|string|max:255',
            'file_size' => 'required|integer|min:1|max:' . (((int) $fileRequest->max_file_size) * 1024 * 1024),
            'mime_type' => 'nullable|string|max:255',
        ]);

        $batch = $this->loadChunkBatchMetadata($fileRequest, $validated['batch_id']);

        if (count($batch['uploaded_files'] ?? []) >= (int) $batch['file_count']) {
            return response()->json([
                'message' => 'Jumlah file melebihi batas yang diizinkan untuk permintaan ini.',
            ], 422);
        }

        $uploadId = (string) Str::uuid();
        $chunkSize = $this->browserUploadChunkSize();
        $totalChunks = max(1, (int) ceil(((int) $validated['file_size']) / $chunkSize));
        $fileDirectory = $this->chunkFileDirectory($fileRequest, $validated['batch_id'], $uploadId);

        Storage::disk('local')->makeDirectory($fileDirectory . '/chunks');
        Storage::disk('local')->put($this->chunkFileMetadataPath($fileRequest, $validated['batch_id'], $uploadId), json_encode([
            'upload_id' => $uploadId,
            'batch_id' => $validated['batch_id'],
            'file_request_id' => $fileRequest->id,
            'file_name' => $validated['file_name'],
            'file_size' => (int) $validated['file_size'],
            'mime_type' => $validated['mime_type'] ?: 'application/octet-stream',
            'chunk_size' => $chunkSize,
            'total_chunks' => $totalChunks,
            'created_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return response()->json([
            'upload_id' => $uploadId,
            'chunk_size' => $chunkSize,
            'total_chunks' => $totalChunks,
        ]);
    }

    public function storeChunk(Request $request, string $slug, string $uploadId): JsonResponse
    {
        $fileRequest = $this->resolvePublicFileRequest($slug);

        $validated = $request->validate([
            'batch_id' => 'required|string',
            'chunk_index' => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1',
            'uploaded_chunk' => 'required|file|max:' . (int) ceil($this->browserUploadChunkSize() / 1024),
        ]);

        $fileUpload = $this->loadChunkFileMetadata($fileRequest, $validated['batch_id'], $uploadId);
        $chunkIndex = (int) $validated['chunk_index'];
        $totalChunks = (int) $validated['total_chunks'];

        if ($totalChunks !== (int) $fileUpload['total_chunks']) {
            return response()->json([
                'message' => 'Jumlah chunk tidak sesuai dengan sesi upload file.',
            ], 422);
        }

        if ($chunkIndex >= $totalChunks) {
            return response()->json([
                'message' => 'Index chunk tidak valid.',
            ], 422);
        }

        $chunkPath = $this->chunkFileDirectory($fileRequest, $validated['batch_id'], $uploadId)
            . '/chunks/' . sprintf('%06d.part', $chunkIndex);

        Storage::disk('local')->put($chunkPath, file_get_contents($validated['uploaded_chunk']->getRealPath()));

        return response()->json([
            'ok' => true,
            'chunk_index' => $chunkIndex,
        ]);
    }

    public function completeChunkFileUpload(Request $request, string $slug, string $uploadId): JsonResponse
    {
        $fileRequest = $this->resolvePublicFileRequest($slug);

        $validated = $request->validate([
            'batch_id' => 'required|string',
        ]);

        $batch = $this->loadChunkBatchMetadata($fileRequest, $validated['batch_id']);
        $fileUpload = $this->loadChunkFileMetadata($fileRequest, $validated['batch_id'], $uploadId);
        $chunkDirectory = $this->chunkFileDirectory($fileRequest, $validated['batch_id'], $uploadId) . '/chunks';

        if (!Storage::disk('local')->exists($chunkDirectory)) {
            return response()->json([
                'message' => 'Chunk upload tidak ditemukan.',
            ], 404);
        }

        $chunkPaths = collect(Storage::disk('local')->files($chunkDirectory))
            ->sort()
            ->values();

        if ($chunkPaths->count() !== (int) $fileUpload['total_chunks']) {
            return response()->json([
                'message' => 'Masih ada bagian file yang belum terkirim. Silakan coba lagi.',
            ], 422);
        }

        $token = UserGoogleToken::ownedByIdentity($fileRequest->ownerIdentityId())->first();
        if (!$token) {
            return response()->json([
                'message' => 'Guru belum menghubungkan Google Drive. Hubungi pemilik link ini.',
            ], 422);
        }

        $this->googleDriveService->setAccessToken($token);
        $studentFolderId = $this->ensureStudentDriveFolder($fileRequest, $batch['class_name'], $batch['name']);

        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $fileUpload['file_name']) ?: ('file_' . now()->timestamp);
        $stagedPath = 'pending-uploads/' . $fileRequest->id . '/' . Str::uuid() . '_' . $safeName;
        $absoluteStagedPath = Storage::disk('local')->path($stagedPath);

        Storage::disk('local')->makeDirectory(dirname($stagedPath));

        $outputHandle = fopen($absoluteStagedPath, 'wb');
        if ($outputHandle === false) {
            throw new \RuntimeException('Gagal menyiapkan file sementara untuk upload besar.');
        }

        try {
            foreach ($chunkPaths as $chunkPath) {
                $inputHandle = fopen(Storage::disk('local')->path($chunkPath), 'rb');
                if ($inputHandle === false) {
                    throw new \RuntimeException('Gagal membaca bagian file upload.');
                }

                try {
                    stream_copy_to_stream($inputHandle, $outputHandle);
                } finally {
                    fclose($inputHandle);
                }
            }
        } finally {
            fclose($outputHandle);
        }

        $task = UploadTask::create([
            'file_request_id' => $fileRequest->id,
            ...UploadTask::ownerAttributes($fileRequest->ownerIdentityId()),
            'submitter_name' => $batch['name'] . ' (' . $batch['class_name'] . ')',
            'class_name' => $batch['class_name'],
            'student_notes' => $batch['notes'] ?? null,
            'original_filename' => $fileUpload['file_name'],
            'mime_type' => $fileUpload['mime_type'] ?: 'application/octet-stream',
            'file_size' => (int) $fileUpload['file_size'],
            'staged_path' => $stagedPath,
            'student_folder_id' => $studentFolderId,
            'status' => 'queued',
            'queued_at' => now(),
        ]);

        UploadSubmissionToDriveJob::dispatch($task->id)->onQueue('uploads');

        $batch['uploaded_files'][] = $fileUpload['file_name'];
        $this->storeChunkBatchMetadata($fileRequest, $validated['batch_id'], $batch);
        Storage::disk('local')->deleteDirectory($this->chunkFileDirectory($fileRequest, $validated['batch_id'], $uploadId));

        return response()->json([
            'ok' => true,
            'file_name' => $fileUpload['file_name'],
        ]);
    }

    public function finishChunkBatch(Request $request, string $slug, string $batchId): JsonResponse
    {
        $fileRequest = $this->resolvePublicFileRequest($slug);
        $batch = $this->loadChunkBatchMetadata($fileRequest, $batchId);
        $uploadedFiles = array_values(array_filter($batch['uploaded_files'] ?? []));

        if (count($uploadedFiles) === 0) {
            return response()->json([
                'message' => 'Belum ada file yang berhasil dikirim ke server.',
            ], 422);
        }

        $runnerUrl = URL::temporarySignedRoute(
            'queue.trigger',
            now()->addMinutes(30),
            ['runs' => min(3, max(1, count($uploadedFiles)))]
        );

        session()->flash('submission_details', [
            'name' => $batch['name'],
            'class' => $batch['class_name'],
            'notes' => $batch['notes'] ?? null,
            'files' => $uploadedFiles,
            'is_queued' => true,
            'runner_url' => $runnerUrl,
        ]);

        Storage::disk('local')->deleteDirectory($this->chunkBatchDirectory($fileRequest, $batchId));

        return response()->json([
            'ok' => true,
            'redirect_url' => route('file-requests.upload', $slug),
        ]);
    }

    public function storePublicUpload(Request $request, $slug)
    {
        $fileRequest = FileRequest::where('slug', $slug)->firstOrFail();

        // Check if active
        if (!$fileRequest->is_active) {
            return redirect()->route('file-requests.upload', $slug)
                ->with('error', 'Permintaan file ini sudah tidak aktif.');
        }
        
        $request->validate([
            'name'       => 'required|string|max:255',
            'class_name' => 'required|string|max:255',
            'notes'      => 'nullable|string|max:1000',
            'files'      => 'required|array|min:1|max:' . max(1, (int) $fileRequest->max_files),
            'files.*'    => 'required|file|max:' . (((int) $fileRequest->max_file_size) * 1024),
        ]);

        // Ambil token guru — tidak filter expires_at agar bisa auto-refresh
        $token = UserGoogleToken::ownedByIdentity($fileRequest->ownerIdentityId())->first();

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
                if (!$file->isValid()) {
                    throw new \RuntimeException($file->getErrorMessage());
                }

                $origName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType() ?: 'application/octet-stream';
                $fileSize = (int) $file->getSize();
                $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $origName) ?: ('file_' . now()->timestamp);
                $stagedPath = $file->storeAs(
                    'pending-uploads/' . $fileRequest->id,
                    Str::uuid() . '_' . $safeName,
                    'local'
                );

                $task = UploadTask::create([
                    'file_request_id' => $fileRequest->id,
                    ...UploadTask::ownerAttributes($fileRequest->ownerIdentityId()),
                    'submitter_name' => $request->name . ' (' . $request->class_name . ')',
                    'class_name' => $request->class_name,
                    'student_notes' => $request->notes,
                    'original_filename' => $origName,
                    'mime_type' => $mimeType,
                    'file_size' => $fileSize,
                    'staged_path' => $stagedPath,
                    'student_folder_id' => $studentFolderId,
                    'status' => 'queued',
                    'queued_at' => now(),
                ]);

                UploadSubmissionToDriveJob::dispatch($task->id)->onQueue('uploads');

                $uploadedFileNames[] = $origName;
            }

            $runnerUrl = URL::temporarySignedRoute(
                'queue.trigger',
                now()->addMinutes(30),
                ['runs' => 1]
            );

            return redirect()->route('file-requests.upload', $slug)
                ->with('submission_details', [
                    'name' => $request->name,
                    'class' => $request->class_name,
                    'notes' => $request->notes,
                    'files' => $uploadedFileNames,
                    'is_queued' => true,
                    'runner_url' => $runnerUrl,
                ]);

        } catch (\Exception $e) {
            Log::error('Public upload staging failed', [
                'slug' => $slug,
                'teacher_id' => $fileRequest->teacher_id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('file-requests.upload', $slug)
                ->with('error', 'Gagal mengupload file: ' . $e->getMessage())
                ->withInput();
        }
    }

    protected function resolvePublicFileRequest(string $slug): FileRequest
    {
        $fileRequest = FileRequest::where('slug', $slug)->firstOrFail();

        if (!$fileRequest->is_active) {
            abort(404, 'File request is no longer active.');
        }

        return $fileRequest;
    }

    protected function browserUploadChunkSize(): int
    {
        return 5 * 1024 * 1024;
    }

    protected function chunkBatchDirectory(FileRequest $fileRequest, string $batchId): string
    {
        return 'chunked-uploads/' . $fileRequest->id . '/' . $batchId;
    }

    protected function chunkBatchMetadataPath(FileRequest $fileRequest, string $batchId): string
    {
        return $this->chunkBatchDirectory($fileRequest, $batchId) . '/batch.json';
    }

    protected function chunkFileDirectory(FileRequest $fileRequest, string $batchId, string $uploadId): string
    {
        return $this->chunkBatchDirectory($fileRequest, $batchId) . '/files/' . $uploadId;
    }

    protected function chunkFileMetadataPath(FileRequest $fileRequest, string $batchId, string $uploadId): string
    {
        return $this->chunkFileDirectory($fileRequest, $batchId, $uploadId) . '/upload.json';
    }

    protected function loadChunkBatchMetadata(FileRequest $fileRequest, string $batchId): array
    {
        $path = $this->chunkBatchMetadataPath($fileRequest, $batchId);

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Sesi upload tidak ditemukan atau sudah kedaluwarsa.');
        }

        return json_decode(Storage::disk('local')->get($path), true, flags: JSON_THROW_ON_ERROR);
    }

    protected function storeChunkBatchMetadata(FileRequest $fileRequest, string $batchId, array $payload): void
    {
        Storage::disk('local')->put(
            $this->chunkBatchMetadataPath($fileRequest, $batchId),
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
        );
    }

    protected function loadChunkFileMetadata(FileRequest $fileRequest, string $batchId, string $uploadId): array
    {
        $path = $this->chunkFileMetadataPath($fileRequest, $batchId, $uploadId);

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Sesi file upload tidak ditemukan atau sudah kedaluwarsa.');
        }

        return json_decode(Storage::disk('local')->get($path), true, flags: JSON_THROW_ON_ERROR);
    }

    protected function ensureStudentDriveFolder(FileRequest $fileRequest, string $className, string $studentName): string
    {
        $rawFolderName = $className . '_' . $studentName;
        $studentFolderName = preg_replace('/[^A-Za-z0-9 _\-]/', '', $rawFolderName);
        $studentFolderName = trim($studentFolderName) ?: 'Siswa_' . now()->timestamp;

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

        return $studentFolderId;
    }
}
