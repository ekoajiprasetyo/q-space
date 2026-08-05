<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortLink;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\StaticQrCode;

class QrCodeController extends Controller
{
    public function index()
    {
        $dynamicQrs = ShortLink::ownedByIdentity((int) Auth::id())
            ->fromSource(ShortLink::SOURCE_QR_DYNAMIC)
            ->latest()
            ->get();

        $textQrs = \App\Models\QrText::ownedByIdentity((int) Auth::id())
            ->latest()
            ->get();

        $staticQrs = StaticQrCode::ownedByIdentity((int) Auth::id())
            ->latest()
            ->get();

        return view('codes.index', compact('dynamicQrs', 'textQrs', 'staticQrs'));
    }

    public function storeDynamic(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'qr_options' => ['nullable', 'array'],
        ]);

        do {
            $shortCode = Str::random(6);
        } while (ShortLink::where('short_code', $shortCode)->exists());

        ShortLink::create([
            ...ShortLink::ownerAttributes((int) Auth::id()),
            'name' => $validated['name'],
            'original_url' => $validated['url'],
            'short_code' => $shortCode,
            'is_active' => true,
            'source' => ShortLink::SOURCE_QR_DYNAMIC,
            'qr_options' => $validated['qr_options'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'short_url' => $this->shortLinkUrl($shortCode),
        ]);
    }

    public function updateDynamic(Request $request, ShortLink $dynamicQr)
    {
        abort_unless($dynamicQr->ownerMatches((int) Auth::id()), 403);
        abort_unless($dynamicQr->source === ShortLink::SOURCE_QR_DYNAMIC, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'original_url' => ['required', 'url', 'max:2048'],
        ]);

        $dynamicQr->update($validated);

        return back()->with('success', 'Tujuan QR Dinamis berhasil diperbarui. QR dan short link tetap sama.');
    }

    public function destroyDynamic(ShortLink $dynamicQr)
    {
        abort_unless($dynamicQr->ownerMatches((int) Auth::id()), 403);
        abort_unless($dynamicQr->source === ShortLink::SOURCE_QR_DYNAMIC, 404);

        $dynamicQr->delete();

        return back()->with('success', 'QR Dinamis berhasil dihapus.');
    }

    public function storeStatic(Request $request)
    {
        $validated = $request->validate([
            'content' => ['required', 'url', 'max:2048'],
            'name' => ['required', 'string', 'max:255'],
            'options' => ['required', 'array'],
            'options.dots_type' => ['required', 'string', 'max:50'],
            'options.corners_type' => ['required', 'string', 'max:50'],
            'options.corners_dot_type' => ['required', 'string', 'max:50'],
            'options.foreground_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'options.background_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'options.logo' => ['nullable', 'string', 'max:1048576'],
            'options.frame_style' => ['nullable', 'string', 'in:none,bottom_label,top_label,border_label'],
            'options.frame_text' => ['nullable', 'string', 'max:80'],
            'options.frame_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'options.frame_text_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $options = $validated['options'];
        $fingerprint = hash('sha256', json_encode([
            'content' => $validated['content'],
            'options' => $options,
        ], JSON_UNESCAPED_SLASHES));

        $qr = StaticQrCode::firstOrCreate(
            [
                ...StaticQrCode::ownerAttributes((int) Auth::id()),
                'fingerprint' => $fingerprint,
            ],
            [
                'name' => $validated['name'],
                'content' => $validated['content'],
                'options' => $options,
            ],
        );

        return response()->json([
            'success' => true,
            'created' => $qr->wasRecentlyCreated,
            'id' => $qr->id,
        ]);
    }

    public function destroyStatic(StaticQrCode $staticQrCode)
    {
        abort_unless($staticQrCode->ownerMatches((int) Auth::id()), 403);

        $staticQrCode->delete();

        return back()->with('success', 'QR Code berhasil dihapus.');
    }

    private function shortLinkUrl(string $shortCode): string
    {
        return sprintf('https://%s/%s', config('app.shortlink_domain'), $shortCode);
    }
}
