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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'theme' => 'nullable|string|in:default,dark,elegant,colorful',
            'qr_options' => 'nullable|array',
        ]);

        $qrText = QrText::create([
            ...QrText::ownerAttributes((int) Auth::id()),
            'title' => $request->title,
            'content' => $request->content,
            'theme' => $request->theme ?? 'default',
            'qr_options' => $request->qr_options,
        ]);

        return response()->json([
            'success' => true,
            'url' => $qrText->url,
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

    public function update(Request $request, QrText $qrText)
    {
        abort_unless($qrText->ownerMatches((int) Auth::id()), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'theme' => 'required|string|in:default,dark,elegant,colorful',
        ]);

        $qrText->update($validated);

        return back()->with('success', 'Konten QR Teks berhasil diperbarui. QR dan short link tetap sama.');
    }
}
