@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Menu</h2>
        <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama Menu:</strong> {{ $menu->nama_menu }}</p>
                    <p><strong>Kategori:</strong> {{ ucfirst($menu->kategori) }}</p>
                    <p><strong>Harga:</strong> Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                    <p><strong>Stok:</strong> {{ $menu->stok }}</p>
                    <p><strong>Status:</strong> {{ str_replace('_', ' ', $menu->status) }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Deskripsi:</strong></p>
                    <p>{{ $menu->deskripsi ?? '-' }}</p>
                    @if ($menu->gambar)
                        <img src="{{ asset('storage/' . $menu->gambar) }}" alt="{{ $menu->nama_menu }}" class="img-fluid rounded">
                    @else
                        <p class="text-muted">Tidak ada gambar.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
