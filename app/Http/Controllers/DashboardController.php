<?php

namespace App\Http\Controllers;

use App\Models\FileRequest;
use App\Models\FileSubmission;
use App\Models\ShortLink;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Restriction: Only 'guru' and 'admin' can access Q-Space Dashboard
        if (!in_array($user->role, ['guru', 'admin'])) {
            return redirect()->route('welcome')->with('error', 'Akses Ditolak. Siswa tidak mempunyai akses ke halaman dashboard.'); 
        }
        
        $ownedFileRequests = FileRequest::ownedByTeacherIdentity((int) $user->id);
        $ownedShortLinks = ShortLink::ownedByIdentity((int) $user->id);

        $filesCount = (clone $ownedFileRequests)->count();
        $linksCount = (clone $ownedShortLinks)->count();

        // Statistics
        $fileRequestIds = (clone $ownedFileRequests)->pluck('id');

        $totalSubmissions = FileSubmission::whereIn('file_request_id', $fileRequestIds)->count();

        $totalVisits = (clone $ownedShortLinks)->sum('visits');

        $recentSubmissions = FileSubmission::whereIn('file_request_id', $fileRequestIds)
            ->with('fileRequest:id,title')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $topLinks = (clone $ownedShortLinks)
            ->where('visits', '>', 0)
            ->orderByDesc('visits')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'filesCount',
            'linksCount',
            'totalSubmissions',
            'totalVisits',
            'recentSubmissions',
            'topLinks'
        ));
    }
}
