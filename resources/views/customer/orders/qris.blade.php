@extends('layouts.app')

@section('title', 'Pembayaran QRIS - DeCafe')

@section('content')
<div class="container d-flex flex-column align-items-center justify-content-center py-5">
    <div class="card border-0 shadow-lg p-4 text-center" style="max-width: 480px; width: 100%; border-radius: 20px; border: 1px solid var(--border-color) !important;">
        
        <!-- Header -->
        <div class="mb-3">
            <h4 class="fw-bold mb-1">Pembayaran QRIS</h4>
            <p class="text-muted small">Silakan scan QRIS di bawah ini dan unggah bukti transfer Anda</p>
        </div>

        <!-- Info Order -->
        <div class="p-3 mb-4 rounded-3 text-start" style="background: var(--list-item-bg); border: 1px solid var(--border-color);">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Kode Pesanan</span>
                <span class="fw-semibold">{{ $order->kode_pesanan }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Nomor Meja</span>
                <span class="fw-semibold">Meja {{ $order->nomor_meja }}</span>
            </div>
            <hr class="my-2" style="border-color: var(--border-color); opacity: 1;">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted fw-bold">Total Tagihan</span>
                <span class="fs-4 fw-bold text-success">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- QRIS Code Container -->
        <div class="position-relative bg-white p-3 rounded-4 mx-auto mb-4" style="max-width: 280px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <img src="{{ asset('images/qris.jpg') }}?v={{ time() }}" alt="QRIS Barcode" class="img-fluid rounded-3" style="width: 100%; height: auto; display: block;">
        </div>

        @if(session('error'))
            <div class="alert alert-danger mb-3 small">{{ session('error') }}</div>
        @endif

        <!-- Upload Form -->
        <form action="{{ route('customer.orders.qris.upload', $order) }}" method="POST" enctype="multipart/form-data" class="text-start mt-3">
            @csrf
            <div class="mb-3">
                <label for="bukti_pembayaran" class="form-label fw-semibold small">Unggah Bukti Pembayaran (Maks 2MB)</label>
                <input class="form-control" type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" required>
                <div class="form-text text-muted" style="font-size: 0.75rem;">Pastikan gambar bukti pembayaran terlihat jelas dan terbaca.</div>
            </div>
            
            <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                <i class="bi bi-cloud-upload-fill me-1"></i> Kirim Bukti Pembayaran
            </button>
        </form>

        <!-- Back Button -->
        <div class="mt-3">
            <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-link text-muted btn-sm d-block">
                Kembali ke detail pesanan
            </a>
        </div>

    </div>
</div>
@endsection
