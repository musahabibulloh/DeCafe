<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lauk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaukController extends Controller
{
    public function index()
    {
        $lauks = Lauk::orderBy('tipe')->orderBy('nama_lauk')->paginate(10);

        return view('admin.lauks.index', compact('lauks'));
    }

    public function create()
    {
        return view('admin.lauks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lauk' => 'required|string|max:255',
            'tipe' => 'required|in:utama,tambahan',
            'harga' => 'required|integer|min:0',
            'is_premium' => 'nullable|boolean',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['is_premium'] = $request->has('is_premium');

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = \App\Services\SupabaseStorageService::upload($request->file('gambar'), 'lauks');
        }

        Lauk::create($validated);

        return redirect()->route('admin.lauks.index')
            ->with('success', 'Lauk berhasil ditambahkan.');
    }

    public function edit(Lauk $lauk)
    {
        return view('admin.lauks.edit', compact('lauk'));
    }

    public function update(Request $request, Lauk $lauk)
    {
        $validated = $request->validate([
            'nama_lauk' => 'required|string|max:255',
            'tipe' => 'required|in:utama,tambahan',
            'harga' => 'required|integer|min:0',
            'is_premium' => 'nullable|boolean',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['is_premium'] = $request->has('is_premium');

        if ($request->hasFile('gambar')) {
            if ($lauk->gambar) {
                \App\Services\SupabaseStorageService::delete($lauk->gambar);
            }
            $validated['gambar'] = \App\Services\SupabaseStorageService::upload($request->file('gambar'), 'lauks');
        }

        $lauk->update($validated);

        return redirect()->route('admin.lauks.index')
            ->with('success', 'Lauk berhasil diperbarui.');
    }

    public function destroy(Lauk $lauk)
    {
        if ($lauk->gambar) {
            \App\Services\SupabaseStorageService::delete($lauk->gambar);
        }

        $lauk->delete();

        return redirect()->route('admin.lauks.index')
            ->with('success', 'Lauk berhasil dihapus.');
    }
}
