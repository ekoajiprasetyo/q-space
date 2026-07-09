<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use Illuminate\Http\Request;

class ShortLinkController extends Controller
{
    public function index()
    {
        $query = ShortLink::ownedByIdentity((int) auth()->id());

        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_code', 'like', "%{$search}%")
                  ->orWhere('original_url', 'like', "%{$search}%");
            });
        }

        $links = $query->latest()->paginate(10);
        return view('paths.index', compact('links'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url',
            'name' => 'nullable|string|max:255',
            'short_code' => 'nullable|alpha_dash|unique:short_links,short_code',
        ], [
            'original_url.required' => 'URL asli wajib diisi.',
            'original_url.url' => 'Format URL tidak valid.',
            'short_code.unique' => 'Kode sudah digunakan. Silakan pilih yang lain.',
            'short_code.alpha_dash' => 'Kode alias hanya boleh berisi huruf, angka, strip, dan garis bawah.',
        ]);

        ShortLink::create([
            ...ShortLink::ownerAttributes((int) auth()->id()),
            'name' => $request->name,
            'original_url' => $request->original_url,
            'short_code' => $request->short_code ?? \Illuminate\Support\Str::random(6),
        ]);

        return redirect()->route('paths.index')->with('success', 'Path berhasil dibuat!');
    }

    public function destroy(ShortLink $path)
    {
        if (! $path->ownerMatches((int) auth()->id())) {
            abort(403);
        }
        $path->delete();
        return redirect()->route('paths.index')->with('success', 'Path berhasil dihapus.');
    }

    public function redirect($code)
    {
        $link = \App\Models\ShortLink::where('short_code', $code)->firstOrFail();
        if (!$link->is_active) {
            abort(404);
        }
        $link->increment('visits');
        return redirect($link->original_url);
    }

    public function update(Request $request, ShortLink $path)
    {
        if (! $path->ownerMatches((int) auth()->id())) {
            abort(403);
        }

        $request->validate([
            'original_url' => 'required|url',
            'short_code' => 'required|alpha_dash|unique:short_links,short_code,' . $path->id,
        ], [
            'original_url.required' => 'URL asli wajib diisi.',
            'original_url.url' => 'Format URL tidak valid.',
            'short_code.required' => 'Short Link wajib diisi.',
            'short_code.unique' => 'Kode sudah digunakan. Silakan pilih yang lain.',
            'short_code.alpha_dash' => 'Kode alias hanya boleh berisi huruf, angka, strip, dan garis bawah.',
        ]);

        $path->update([
            'original_url' => $request->original_url,
            'short_code' => $request->short_code,
        ]);

        return redirect()->route('paths.index')->with('success', 'Path berhasil diperbarui!');
    }
}
