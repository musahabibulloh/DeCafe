@extends('layouts.app')

@section('content')
    <h2 class="mb-4">Dashboard Admin</h2>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Menu</p>
                    <h4 class="mb-0">{{ $totalMenu }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total User</p>
                    <h4 class="mb-0">{{ $totalUser }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Pesanan Hari Ini</p>
                    <h4 class="mb-0">{{ $pesananHariIni }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Pendapatan Hari Ini</p>
                    <h4 class="mb-0">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Transaksi</p>
                    <h4 class="mb-0">{{ $totalTransaksi }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Pesanan Belum Dibayar</p>
                    <h4 class="mb-0">{{ $pesananBelumBayar }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Menu Terlaris</h5>
                    <ul class="list-group list-group-flush">
                        @forelse ($menuTerlaris as $menu)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $menu->nama_menu }}</span>
                                <span class="fw-semibold">{{ $menu->total_terjual }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Belum ada data.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Ringkasan Pesanan</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Pesanan Selesai</span>
                            <span class="fw-semibold">{{ $pesananSelesai }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Pesanan Belum Dibayar</span>
                            <span class="fw-semibold">{{ $pesananBelumBayar }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
