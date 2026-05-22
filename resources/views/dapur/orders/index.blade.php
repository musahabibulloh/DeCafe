@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Pesanan Masuk</h2>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Desktop View -->
            <div class="d-none d-md-block table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Meja</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ $order->kode_pesanan }}</td>
                                <td>{{ $order->nomor_meja }}</td>
                                <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status_pesanan === 'menunggu' ? 'warning' : ($order->status_pesanan === 'diproses' ? 'info' : 'secondary') }}">
                                        {{ str_replace('_', ' ', $order->status_pesanan) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('dapur.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada pesanan.</td>
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
                                <span class="badge bg-{{ $order->status_pesanan === 'menunggu' ? 'warning' : ($order->status_pesanan === 'diproses' ? 'info' : 'secondary') }}">
                                    {{ str_replace('_', ' ', $order->status_pesanan) }}
                                </span>
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
                            <div class="d-grid gap-2 d-flex justify-content-end mt-2 pt-2 border-top" style="border-top-color: rgba(var(--bs-body-color-rgb), 0.1) !important;">
                                <a href="{{ route('dapur.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary px-3">Detail</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Belum ada pesanan.</div>
                @endforelse
            </div>

            {{ $orders->links() }}
        </div>
    </div>
@endsection
