<?php

namespace App\Http\Controllers;

use App\Models\QrText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrTextController extends Controller
{
    /**
     * Store a new QR Text
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'theme' => 'nullable|string|in:default,dark,elegant,colorful',
        ]);

        $qrText = QrText::create([
            ...QrText::ownerAttributes((int) Auth::id()),
            'title' => $request->title,
            'content' => $request->content,
            'theme' => $request->theme ?? 'default',
        ]);

        return response()->json([
            'success' => true,
            'url' => route('qr-text.show', $qrText->slug),
            'slug' => $qrText->slug,
        ]);
    }

    /**
     * Display the QR Text content (public)
     */
    public function show(string $slug)
    {
        $qrText = QrText::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Increment views
        $qrText->increment('views');

        return view('codes.text-view', compact('qrText'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $qrText = QrText::findOrFail($id);

        if (! $qrText->ownerMatches((int) Auth::id())) {
            abort(403);
        }

        $qrText->delete();

        return back()->with('success', 'QR Code berhasil dihapus.');
    }
}
