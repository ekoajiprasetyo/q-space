<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FileRequest;
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
        
        $filesCount = \App\Models\FileRequest::where('teacher_id', $user->id)->count();
        $linksCount = \App\Models\ShortLink::where('user_id', $user->id)->count();

        return view('dashboard', compact('filesCount', 'linksCount'));
    }
}
