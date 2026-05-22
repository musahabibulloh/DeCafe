@extends('layouts.app')

@section('title', 'Pesanan Saya - DeCafe')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Pesanan Saya</h1>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-success">Pesan Lagi</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($orders->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted">Belum ada pesanan.</p>
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">Buat Pesanan Pertama</a>
                </div>
            @else
                <!-- Desktop View -->
                <div class="d-none d-md-block table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>Meja</th>
                                <th>Total</th>
                                <th>Status Pesanan</th>
                                <th>Status Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td><strong>{{ $order->kode_pesanan }}</strong></td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $order->nomor_meja }}</td>
                                    <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        @switch($order->status_pesanan)
                                            @case('menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                                @break
                                            @case('diterima_dapur')
                                                <span class="badge bg-info">Dikonfirmasi Dapur</span>
                                                @break
                                            @case('diproses')
                                                <span class="badge bg-primary">Diproses</span>
                                                @break
                                            @case('siap_saji')
                                                <span class="badge bg-success">Siap Saji</span>
                                                @break
                                            @case('selesai')
                                                <span class="badge bg-secondary">Selesai</span>
                                                @break
                                            @case('dibatalkan')
                                                <span class="badge bg-danger">Dibatalkan</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($order->status_pembayaran === 'lunas')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-danger">Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-primary">Detail</a>
                                        @if($order->status_pembayaran !== 'lunas' && $order->status_pesanan !== 'dibatalkan')
                                            <form action="{{ route('customer.orders.pay', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Bayar pesanan ini?')">Bayar</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile View -->
                <div class="d-md-none">
                    @foreach($orders as $order)
                        <div class="card border-0 shadow-sm mb-3" style="background-color: var(--bs-card-bg); border: 1px solid rgba(var(--bs-body-color-rgb), 0.1) !important;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-primary">{{ $order->kode_pesanan }}</h6>
                                    <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <hr class="my-2 opacity-50" style="color: var(--bs-body-color);">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Meja</small>
                                        <span class="fw-semibold">{{ $order->nomor_meja }}</span>
                                    </div>
                                    <div class="col-6 text-end">
                                        <small class="text-muted d-block">Total</small>
                                        <span class="fw-bold text-success">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <div>
                                        <small class="text-muted d-block mb-1">Status Pesanan</small>
                                        @switch($order->status_pesanan)
                                            @case('menunggu')
                                                <span class="badge bg-warning">Menunggu</span>
                                                @break
                                            @case('diterima_dapur')
                                                <span class="badge bg-info">Dikonfirmasi Dapur</span>
                                                @break
                                            @case('diproses')
                                                <span class="badge bg-primary">Diproses</span>
                                                @break
                                            @case('siap_saji')
                                                <span class="badge bg-success">Siap Saji</span>
                                                @break
                                            @case('selesai')
                                                <span class="badge bg-secondary">Selesai</span>
                                                @break
                                            @case('dibatalkan')
                                                <span class="badge bg-danger">Dibatalkan</span>
                                                @break
                                        @endswitch
                                    </div>
                                    <div class="ms-auto text-end">
                                        <small class="text-muted d-block mb-1">Pembayaran</small>
                                        @if($order->status_pembayaran === 'lunas')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-danger">Belum Bayar</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-grid gap-2 d-flex justify-content-end mt-2 pt-2 border-top" style="border-top-color: rgba(var(--bs-body-color-rgb), 0.1) !important;">
                                    <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-primary px-3">Detail</a>
                                    @if($order->status_pembayaran !== 'lunas' && $order->status_pesanan !== 'dibatalkan')
                                        <form action="{{ route('customer.orders.pay', $order) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success px-3" onclick="return confirm('Bayar pesanan ini?')">Bayar</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{ $orders->links() }}
            @endif
        </div>
    </div>
</div>
@endsection