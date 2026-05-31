@extends('layouts.app')

@section('title', 'Detail Pesanan - Nasi Bakar Cak Win')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Detail Pesanan</h1>
        <a href="{{ route('customer.orders.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Kode Pesanan:</strong> {{ $order->kode_pesanan }}</p>
                            <p><strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Atas Nama:</strong> {{ $order->atas_nama ?? '-' }}</p>
                            <p><strong>Nomor Meja:</strong> {{ $order->nomor_meja }}</p>
                            <p><strong>Tipe Pesanan:</strong>
                                @if($order->tipe_pesanan === 'take_away')
                                    <span class="badge bg-info"><i class="bi bi-bag me-1"></i>Take Away (Bungkus)</span>
                                @else
                                    <span class="badge bg-primary"><i class="bi bi-shop me-1"></i>Dine In (Makan di Tempat)</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status Pesanan:</strong>
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
                            </p>
                            <p><strong>Status Pembayaran:</strong>
                                @if($order->status_pembayaran === 'lunas')
                                    <span class="badge bg-success">Lunas</span>
                                @elseif($order->status_pembayaran === 'menunggu_konfirmasi')
                                    <span class="badge bg-warning text-dark">Menunggu Konfirmasi Kasir</span>
                                @else
                                    <span class="badge bg-danger">Belum Bayar</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Item Pesanan</h5>
                </div>
                <div class="card-body">
                    <!-- Desktop Table -->
                    <table class="table d-none d-md-table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                             @foreach($order->items as $item)
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
                                <th colspan="3" class="text-end">Total:</th>
                                <th>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Mobile List -->
                    <div class="d-md-none list-group list-group-flush">
                        @foreach($order->items as $item)
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
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Pembayaran</h5>
                </div>
                <div class="card-body text-center">
                    @if($order->status_pembayaran === 'lunas')
                        <div class="alert alert-success">
                            <h4>LUNAS</h4>
                            <p class="mb-0">Pesanan sudah dibayar</p>
                        </div>
                        @if($order->payment)
                            <p><strong>Metode:</strong> {{ strtoupper($order->payment->metode_pembayaran) }}</p>
                            <p><strong>Waktu Bayar:</strong> {{ $order->payment->paid_at->format('d/m/Y H:i') }}</p>
                        @endif
                    @elseif($order->status_pembayaran === 'menunggu_konfirmasi')
                        <div class="alert alert-info">
                            <h5 class="fw-bold"><i class="bi bi-hourglass-split me-1"></i> MENUNGGU VERIFIKASI</h5>
                            <p class="mb-0 small">Bukti pembayaran telah diunggah. Kasir sedang memverifikasi pembayaran Anda.</p>
                        </div>
                        @if($order->bukti_pembayaran)
                            <div class="mt-3 text-start">
                                <label class="form-label fw-bold small text-muted">Bukti Pembayaran Anda:</label>
                                <a href="{{ asset($order->bukti_pembayaran) }}" target="_blank" class="d-block border rounded p-2 text-center bg-dark text-decoration-none">
                                    <img src="{{ asset($order->bukti_pembayaran) }}" class="img-fluid rounded" style="max-height: 120px;" alt="Bukti Pembayaran">
                                    <span class="d-block small text-muted mt-1"><i class="bi bi-eye"></i> Lihat Ukuran Penuh</span>
                                </a>
                            </div>
                        @endif
                    @elseif($order->status_pesanan !== 'dibatalkan')
                        <div class="alert alert-warning">
                            <h4>BELUM BAYAR</h4>
                            <p class="mb-0">Total: Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                        </div>
                        <form action="{{ route('customer.orders.pay', $order) }}" method="POST" id="paymentForm">
                            @csrf
                            <div class="mb-4 text-start">
                                <label class="form-label fw-bold d-block mb-3">Pilih Metode Pembayaran</label>
                                
                                <div class="row g-3">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="metode" id="pay_qris" value="qris" checked>
                                        <label class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center gap-2" for="pay_qris" style="border-width: 2px;">
                                            <i class="bi bi-qr-code-scan fs-3"></i>
                                            <span class="fw-bold">QRIS</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="metode" id="pay_cash" value="cash">
                                        <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-2" for="pay_cash" style="border-width: 2px;">
                                            <i class="bi bi-cash-coin fs-3"></i>
                                            <span class="fw-bold">Tunai (Kasir)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="cashInstructions" class="alert alert-info text-start small d-none" style="border-left: 4px solid #0dcaf0; background: rgba(13, 202, 240, 0.1); color: #0dcaf0;">
                                <i class="bi bi-info-circle-fill me-1"></i> Silakan informasikan Kode Pesanan <strong>{{ $order->kode_pesanan }}</strong> ke Kasir untuk membayar secara tunai.
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-lg w-100 mt-2" id="btnPay">
                                Lanjutkan Pembayaran <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </form>

                        <script>
                            document.querySelectorAll('input[name="metode"]').forEach(radio => {
                                radio.addEventListener('change', function() {
                                    const val = this.value;
                                    const cashInstruct = document.getElementById('cashInstructions');
                                    const btnPay = document.getElementById('btnPay');
                                    
                                    if (val === 'cash') {
                                        cashInstruct.classList.remove('d-none');
                                        btnPay.innerHTML = '<i class="bi bi-cash-coin me-1"></i> Konfirmasi Pembayaran Tunai';
                                        btnPay.className = 'btn btn-primary btn-lg w-100 mt-2';
                                    } else {
                                        cashInstruct.classList.add('d-none');
                                        btnPay.innerHTML = 'Lanjutkan Pembayaran <i class="bi bi-arrow-right ms-1"></i>';
                                        btnPay.className = 'btn btn-success btn-lg w-100 mt-2';
                                    }
                                });
                            });
                        </script>
                    @else
                        <div class="alert alert-danger">
                            <h4>DIBATALKAN</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($order->status_pembayaran === 'menunggu_konfirmasi')
    @push('scripts')
    <script>
        const checkStatusInterval = setInterval(checkStatus, 3000);

        function checkStatus() {
            fetch("{{ route('customer.orders.status', $order) }}")
                .then(res => res.json())
                .then(data => {
                    if (data.status_pembayaran === 'lunas') {
                        clearInterval(checkStatusInterval);
                        location.reload();
                    }
                })
                .catch(err => console.error("Error checking status:", err));
        }
    </script>
    @endpush
@endif
@endsection