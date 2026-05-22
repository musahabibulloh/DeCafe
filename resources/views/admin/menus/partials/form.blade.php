<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Menu</label>
        <input type="text" name="nama_menu" class="form-control" value="{{ old('nama_menu', $menu->nama_menu ?? '') }}" required>
        @error('nama_menu')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Kategori</label>
        <select name="kategori" class="form-select" required>
            <option value="">Pilih Kategori</option>
            <option value="makanan" @selected(old('kategori', $menu->kategori ?? '') === 'makanan')>Makanan</option>
            <option value="minuman" @selected(old('kategori', $menu->kategori ?? '') === 'minuman')>Minuman</option>
        </select>
        @error('kategori')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Harga</label>
        <input type="number" name="harga" class="form-control" value="{{ old('harga', $menu->harga ?? '') }}" min="0" required>
        @error('harga')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Stok</label>
        <input type="number" name="stok" class="form-control" value="{{ old('stok', $menu->stok ?? 0) }}" min="0" required>
        @error('stok')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="tersedia" @selected(old('status', $menu->status ?? 'tersedia') === 'tersedia')>Tersedia</option>
            <option value="habis" @selected(old('status', $menu->status ?? '') === 'habis')>Habis</option>
            <option value="nonaktif" @selected(old('status', $menu->status ?? '') === 'nonaktif')>Nonaktif</option>
        </select>
        @error('status')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Gambar</label>
        <input type="file" name="gambar" class="form-control">
        @error('gambar')
            <small class="text-danger">{{ $message }}</small>
        @enderror
        @if (!empty($menu?->gambar))
            <small class="text-muted">Gambar saat ini: {{ $menu->gambar }}</small>
        @endif
    </div>
    <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $menu->deskripsi ?? '') }}</textarea>
    </div>
</div>
<hr class="my-4">
