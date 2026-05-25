@extends('layouts.app')

@section('content')
    <style>
        .dashboard-stat-link {
            display: block;
            height: 100%;
            color: inherit;
            text-decoration: none;
            border-radius: 16px;
        }

        .dashboard-stat-link .card {
            height: 100%;
        }

        .dashboard-stat-link:focus-visible {
            outline: 3px solid var(--primary-color);
            outline-offset: 4px;
        }

        .dashboard-stat-link .stat-action {
            color: var(--primary-color);
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>

    <h2 class="mb-4">Dashboard Admin</h2>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('admin.menus.index') }}" class="dashboard-stat-link" aria-label="Buka Kelola Menu">
                <div class="card card-stat shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Menu</p>
                        <h4 class="mb-2">{{ $totalMenu }}</h4>
                        <span class="stat-action">Kelola Menu <i class="bi bi-arrow-right-short"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.users.index') }}" class="dashboard-stat-link" aria-label="Buka Kelola User">
                <div class="card card-stat shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total User</p>
                        <h4 class="mb-2">{{ $totalUser }}</h4>
                        <span class="stat-action">Kelola User <i class="bi bi-arrow-right-short"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.reports.index', ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString()]) }}" class="dashboard-stat-link" aria-label="Buka Laporan Pesanan Hari Ini">
                <div class="card card-stat shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Pesanan Hari Ini</p>
                        <h4 class="mb-2">{{ $pesananHariIni }}</h4>
                        <span class="stat-action">Lihat Laporan <i class="bi bi-arrow-right-short"></i></span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('admin.reports.index', ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString()]) }}" class="dashboard-stat-link" aria-label="Buka Laporan Pendapatan Hari Ini">
                <div class="card card-stat shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Pendapatan Hari Ini</p>
                        <h4 class="mb-2">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h4>
                        <span class="stat-action">Lihat Laporan <i class="bi bi-arrow-right-short"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.reports.index') }}" class="dashboard-stat-link" aria-label="Buka Laporan Total Transaksi">
                <div class="card card-stat shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Transaksi</p>
                        <h4 class="mb-2">{{ $totalTransaksi }}</h4>
                        <span class="stat-action">Lihat Laporan <i class="bi bi-arrow-right-short"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.reports.index') }}" class="dashboard-stat-link" aria-label="Buka Laporan Pesanan Belum Dibayar">
                <div class="card card-stat shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Pesanan Belum Dibayar</p>
                        <h4 class="mb-2">{{ $pesananBelumBayar }}</h4>
                        <span class="stat-action">Lihat Laporan <i class="bi bi-arrow-right-short"></i></span>
                    </div>
                </div>
            </a>
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
