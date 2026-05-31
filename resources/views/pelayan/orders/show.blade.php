@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Pesanan</h2>
        <a href="{{ route('pelayan.orders.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Kode Pesanan:</strong> {{ $order->kode_pesanan }}</p>
            <p><strong>Nomor Meja:</strong> {{ $order->nomor_meja }}</p>
            <p><strong>Atas Nama:</strong> {{ $order->atas_nama ?? '-' }}</p>
            <p><strong>Nama Pelanggan:</strong> {{ $order->nama_pelanggan ?? '-' }}</p>
            <p><strong>Tipe Pesanan:</strong>
                @if($order->tipe_pesanan === 'take_away')
                    <span class="badge bg-info"><i class="bi bi-bag me-1"></i>Take Away (Bungkus)</span>
                @else
                    <span class="badge bg-primary"><i class="bi bi-shop me-1"></i>Dine In (Makan di Tempat)</span>
                @endif
            </p>
            <p><strong>Status Pesanan:</strong> {{ str_replace('_', ' ', $order->status_pesanan) }}</p>
            <p><strong>Status Pembayaran:</strong> {{ str_replace('_', ' ', $order->status_pembayaran) }}</p>
            <p><strong>Catatan:</strong> {{ $order->catatan ?? '-' }}</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Item Pesanan</h5>
            <!-- Desktop View -->
            <div class="d-none d-md-block table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    {{ $item->nama_menu }}
                                    @if($item->catatan_item)
                                        <div class="small text-muted font-monospace mt-1" style="font-size: 0.8rem; white-space: pre-line;">
                                            <i class="bi bi-gear-wide-connected"></i> {!! e($item->catatan_item) !!}
                                        </div>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="d-md-none list-group list-group-flush">
                @foreach ($order->items as $item)
                    <div class="list-group-item px-0 py-2 border-0 border-bottom" style="background: transparent;">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-semibold" style="color: var(--bs-body-color);">
                                {{ $item->nama_menu }}
                                @if($item->catatan_item)
                                    <div class="small text-muted font-monospace mt-1" style="font-size: 0.75rem; white-space: pre-line; font-weight: normal;">
                                        <i class="bi bi-gear-wide-connected"></i> {!! e($item->catatan_item) !!}
                                    </div>
                                @endif
                            </span>
                            <span class="fw-bold text-primary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-muted small">
                            <span>Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                            <span>Jumlah: <strong>{{ $item->jumlah }}x</strong></span>
                        </div>
                    </div>
                @endforeach
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                    <span class="fw-bold" style="color: var(--bs-body-color);">Total:</span>
                    <h5 class="fw-bold text-success mb-0">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>
@endsection
