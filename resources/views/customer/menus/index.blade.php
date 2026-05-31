@extends('layouts.app')

@section('title', 'Menu - Nasi Bakar Cak Win')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Daftar Menu</h1>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-success">Pesan Sekarang</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @foreach($menus as $kategori => $items)
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">{{ $kategori }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($items as $menu)
                        <div class="col-md-4 col-lg-3 mb-3">
                            <div class="card h-100 border">
                                @if($menu->gambar_url)
                                    <img src="{{ $menu->gambar_url }}" class="card-img-top" alt="{{ $menu->nama_menu }}" style="height: 150px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <span class="text-muted">Tanpa Gambar</span>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title mb-1">{{ $menu->nama_menu }}</h6>
                                    <p class="text-primary fw-bold mb-1">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                                    <p class="small text-muted mb-2">Stok: {{ $menu->stok }}</p>
                                    <span class="badge bg-{{ $menu->status === 'tersedia' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($menu->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    @if($menus->isEmpty())
        <div class="alert alert-info">Tidak ada menu tersedia saat ini.</div>
    @endif
</div>
@endsection