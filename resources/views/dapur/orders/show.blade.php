@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Pesanan Dapur</h2>
        <a href="{{ route('dapur.orders.index') }}" class="btn btn-outline-secondary">Kembali</a>
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
            <p><strong>Catatan:</strong> {{ $order->catatan ?? '-' }}</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">Item Pesanan</h5>
            <!-- Desktop View -->
            <div class="d-none d-md-block table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Jumlah</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->nama_menu }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td style="white-space: pre-line;">{{ $item->catatan_item ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="d-md-none list-group list-group-flush">
                @foreach ($order->items as $item)
                    <div class="list-group-item px-0 py-2 border-0 border-bottom" style="background: transparent;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold" style="color: var(--bs-body-color);">{{ $item->nama_menu }}</span>
                            <span class="badge bg-secondary">{{ $item->jumlah }}x</span>
                        </div>
                        <div class="text-muted small" style="white-space: pre-line;">
                            Catatan: {{ $item->catatan_item ?? '-' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        @if ($order->status_pesanan === 'menunggu')
            <form action="{{ route('dapur.orders.accept', $order) }}" method="POST" onsubmit="return confirm('Terima pesanan ini untuk diproses?')">
                @csrf
                <button class="btn btn-warning" type="submit">Terima Pesanan</button>
            </form>
        @endif
        @if ($order->status_pesanan === 'diterima_dapur')
            <form action="{{ route('dapur.orders.process', $order) }}" method="POST" onsubmit="return confirm('Mulai proses masak pesanan ini?')">
                @csrf
                <button class="btn btn-info" type="submit">Proses Pesanan</button>
            </form>
        @endif
        @if ($order->status_pesanan === 'diproses')
            <form action="{{ route('dapur.orders.ready', $order) }}" method="POST" onsubmit="return confirm('Pesanan ini sudah selesai dimasak dan siap disajikan?')">
                @csrf
                <button class="btn btn-success" type="submit">Siap Saji</button>
            </form>
        @endif
    </div>
@endsection
