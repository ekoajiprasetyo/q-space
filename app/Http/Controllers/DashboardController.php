<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FileRequest;
use App\Models\FileSubmission;
use App\Models\ShortLink;
use App\Models\UserGoogleToken;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Restriction: Only 'guru' and 'admin' can access Q-Space Dashboard
        if (!in_array($user->role, ['guru', 'admin'])) {
            return redirect()->route('welcome')->with('error', 'Akses Ditolak. Siswa tidak mempunyai akses ke halaman dashboard.'); 
        }
        
        $filesCount = FileRequest::where('teacher_id', $user->id)->count();
        $linksCount = ShortLink::where('user_id', $user->id)->count();

        // Statistics
        $fileRequestIds = FileRequest::where('teacher_id', $user->id)->pluck('id');

        $totalSubmissions = FileSubmission::whereIn('file_request_id', $fileRequestIds)->count();

        $totalVisits = ShortLink::where('user_id', $user->id)->sum('visits');

        $recentSubmissions = FileSubmission::whereIn('file_request_id', $fileRequestIds)
            ->with('fileRequest:id,title')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $topLinks = ShortLink::where('user_id', $user->id)
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
