@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Laporan Penjualan</h2>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button class="btn btn-primary" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.reports.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Transaksi Selesai</p>
                    <h4 class="mb-0">{{ $totalTransaksi }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pendapatan</p>
                    <h4 class="mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pesanan</p>
                    <h4 class="mb-0">{{ $statusCounts->sum() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
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
                    <h5 class="mb-3">Total Pesanan per Status</h5>
                    <ul class="list-group list-group-flush">
                        @forelse ($statusCounts as $status => $total)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ str_replace('_', ' ', $status) }}</span>
                                <span class="fw-semibold">{{ $total }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Belum ada data.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Daftar Transaksi</h5>
            <!-- Desktop View -->
            <div class="d-none d-md-block table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Kode Pesanan</th>
                            <th>Pelayan</th>
                            <th>Meja</th>
                            <th>Total</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ $order->kode_pesanan }}</td>
                                <td>{{ $order->user->name ?? '-' }}</td>
                                <td>{{ $order->nomor_meja }}</td>
                                <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="d-md-none">
                @forelse ($orders as $order)
                    <div class="card border-0 shadow-sm mb-3" style="background-color: var(--bs-card-bg); border: 1px solid rgba(var(--bs-body-color-rgb), 0.1) !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-primary">{{ $order->kode_pesanan }}</h6>
                                <small class="text-muted">{{ $order->created_at->format('d M Y') }}</small>
                            </div>
                            <hr class="my-2 opacity-50" style="color: var(--bs-body-color);">
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Meja</small>
                                    <span class="fw-semibold">{{ $order->nomor_meja }}</span>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block">Total</small>
                                    <span class="fw-bold text-success">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                </div>
                                <div class="col-12 mt-2">
                                    <small class="text-muted d-block">Pelayan</small>
                                    <span class="fw-semibold">{{ $order->user->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Belum ada transaksi.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
