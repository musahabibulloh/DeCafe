<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('nama_menu')->paginate(10);

        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori' => 'required|in:makanan,minuman',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'status' => 'required|in:tersedia,habis,nonaktif',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'maksimal_lauk' => 'required|integer|min:1|max:10',
            'wajib_pilih_lauk' => 'nullable|boolean',
            'wajib_pilih_sambal' => 'nullable|boolean',
            'options' => 'nullable|array',
            'options.*.nama_opsi' => 'nullable|string|max:255',
            'options.*.tipe' => 'nullable|in:lauk,sambal,ekstra_lauk',
            'options.*.status' => 'nullable|in:tersedia,habis',
        ]);

        $options = $validated['options'] ?? [];
        unset($validated['options']);
        $validated = $this->prepareMenuData($validated);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = \App\Services\SupabaseStorageService::upload($request->file('gambar'), 'menus');
        }

        $menu = Menu::create($validated);
        $this->syncOptions($menu, $options);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function show(Menu $menu)
    {
        $menu->load('options');

        return view('admin.menus.show', compact('menu'));
    }

    public function edit(Menu $menu)
    {
        $menu->load('options');

        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori' => 'required|in:makanan,minuman',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'status' => 'required|in:tersedia,habis,nonaktif',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'maksimal_lauk' => 'required|integer|min:1|max:10',
            'wajib_pilih_lauk' => 'nullable|boolean',
            'wajib_pilih_sambal' => 'nullable|boolean',
            'options' => 'nullable|array',
            'options.*.nama_opsi' => 'nullable|string|max:255',
            'options.*.tipe' => 'nullable|in:lauk,sambal,ekstra_lauk',
            'options.*.status' => 'nullable|in:tersedia,habis',
        ]);

        $options = $validated['options'] ?? [];
        unset($validated['options']);
        $validated = $this->prepareMenuData($validated);

        if ($request->hasFile('gambar')) {
            if ($menu->gambar) {
                \App\Services\SupabaseStorageService::delete($menu->gambar);
            }
            $validated['gambar'] = \App\Services\SupabaseStorageService::upload($request->file('gambar'), 'menus');
        }

        $menu->update($validated);
        $this->syncOptions($menu, $options);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->gambar) {
            \App\Services\SupabaseStorageService::delete($menu->gambar);
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }

    private function prepareMenuData(array $validated): array
    {
        $validated['wajib_pilih_lauk'] = (bool) ($validated['wajib_pilih_lauk'] ?? false);
        $validated['wajib_pilih_sambal'] = (bool) ($validated['wajib_pilih_sambal'] ?? false);

        return $validated;
    }

    private function syncOptions(Menu $menu, array $options): void
    {
        $menu->options()->delete();

        collect($options)
            ->map(fn ($option) => [
                'nama_opsi' => trim($option['nama_opsi'] ?? ''),
                'tipe' => $option['tipe'] ?? 'lauk',
                'status' => $option['status'] ?? 'tersedia',
            ])
            ->filter(fn ($option) => $option['nama_opsi'] !== '')
            ->values()
            ->each(function ($option, $index) use ($menu) {
                $menu->options()->create([
                    'nama_opsi' => $option['nama_opsi'],
                    'tipe' => $option['tipe'],
                    'status' => $option['status'],
                    'sort_order' => $index,
                ]);
            });
    }
}
