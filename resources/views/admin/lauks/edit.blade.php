@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Ubah Lauk / Ekstra</h2>
        <a href="{{ route('admin.lauks.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.lauks.update', $lauk) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lauk / Ekstra <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lauk" class="form-control" value="{{ old('nama_lauk', $lauk->nama_lauk) }}" placeholder="Contoh: Ayam suwir / Telur asin" required>
                        @error('nama_lauk')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Tipe <span class="text-danger">*</span></label>
                        <select name="tipe" id="tipeSelect" class="form-select" required>
                            <option value="">Pilih Tipe</option>
                            <option value="utama" @selected(old('tipe', $lauk->tipe) === 'utama')>Lauk Utama</option>
                            <option value="tambahan" @selected(old('tipe', $lauk->tipe) === 'tambahan')>Ekstra Lauk (Tambahan)</option>
                        </select>
                        @error('tipe')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Harga <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga" class="form-control" value="{{ old('harga', $lauk->harga) }}" min="0" placeholder="Contoh: 12000 / 3000" required>
                        </div>
                        @error('harga')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        <small class="text-muted mt-1 d-block">
                            * Lauk Utama: Harga porsi ketika dipilih di Nasi Bakar Reguler.<br>
                            * Ekstra Lauk: Harga add-on tambahan per porsi.
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Gambar Lauk</label>
                        <input type="file" name="gambar" class="form-control mb-2">
                        @error('gambar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        @if ($lauk->gambar_url)
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <img src="{{ $lauk->gambar_url }}" alt="{{ $lauk->nama_lauk }}" class="img-thumbnail" style="height: 50px; width: 50px; object-fit: cover;">
                                <small class="text-muted">Gambar saat ini</small>
                            </div>
                        @else
                            <small class="text-muted mt-1 d-block">Belum ada gambar (akan menggunakan default/tanpa gambar).</small>
                        @endif
                    </div>

                    <div class="col-md-6" id="premiumContainer">
                        <label class="form-label d-block">Pengaturan Lauk Utama</label>
                        <div class="form-check form-switch pt-2">
                            <input class="form-check-input" type="checkbox" name="is_premium" id="is_premium" value="1" @checked(old('is_premium', $lauk->is_premium))>
                            <label class="form-check-label fw-semibold text-warning" for="is_premium">
                                <i class="bi bi-star-fill me-1"></i> Lauk Premium
                            </label>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            Centang jika lauk premium (contoh: Cumi, Paru, Tetelan) agar sistem memberikan penanda bintang (*) di pilihan lauk utama.
                        </small>
                    </div>
                </div>

                <hr class="my-4">
                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipeSelect = document.getElementById('tipeSelect');
            const premiumContainer = document.getElementById('premiumContainer');

            function togglePremium() {
                if (tipeSelect.value === 'utama') {
                    premiumContainer.style.display = 'block';
                } else {
                    premiumContainer.style.display = 'none';
                    document.getElementById('is_premium').checked = false;
                }
            }

            tipeSelect.addEventListener('change', togglePremium);
            togglePremium(); // run on page load
        });
    </script>
    @endpush
@endsection
