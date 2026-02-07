<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortLink;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class QrCodeController extends Controller
{
    public function index()
    {
        return view('codes.index');
    }

    public function storeDynamic(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $shortCode = Str::random(6);
        
        // Retry once if collision
        if (ShortLink::where('short_code', $shortCode)->exists()) {
            $shortCode = Str::random(6);
        }

        ShortLink::create([
            'user_id' => Auth::id(),
            'name' => 'QR Dynamic - ' . now()->format('d M Y H:i'),
            'original_url' => $request->url,
            'short_code' => $shortCode,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'short_url' => url('/' . $shortCode)
        ]);
    }
}
