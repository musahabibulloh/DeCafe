@extends('layouts.app')

@section('title', 'Kelola Meja & QR Code - Nasi Bakar Cak Win')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Kelola Meja & QR Code</h1>
            <p class="text-muted mb-0">Buat dan kelola QR code untuk setiap meja. Customer tinggal scan untuk langsung pesan.</p>
        </div>
        @if($mejas->count() > 0)
            <a href="{{ route('admin.meja.print-all-qr') }}" class="btn btn-outline-primary d-none d-md-inline-flex align-items-center gap-2" target="_blank">
                <i class="bi bi-printer-fill"></i> Cetak Semua QR
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Add Table Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Meja Baru</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.meja.store') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-6">
                    <label for="nomor_meja" class="form-label fw-bold">Nomor Meja</label>
                    <input type="text" name="nomor_meja" id="nomor_meja" class="form-control" placeholder="Contoh: 1, 2, 3, A1, VIP-01" required>
                    @error('nomor_meja')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Meja
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tables List -->
    @if($mejas->isEmpty())
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
            <h5>Belum ada meja</h5>
            <p class="mb-0 text-muted">Tambahkan meja di atas untuk mulai membuat QR code.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($mejas as $meja)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <!-- QR Code Display -->
                            <div class="mb-3 p-3 rounded-3" style="background: #ffffff;">
                                <div id="qrcode-{{ $meja->id }}" class="d-flex justify-content-center"></div>
                            </div>

                            <h4 class="fw-bold mb-1">Meja {{ $meja->nomor_meja }}</h4>
                            <p class="text-muted small mb-3 text-break" style="font-size: 0.75rem;">
                                {{ $meja->scan_url }}
                            </p>

                            <!-- Actions -->
                            <div class="d-flex gap-2 flex-wrap justify-content-center">
                                <a href="{{ route('admin.meja.print-qr', $meja) }}" class="btn btn-outline-primary btn-sm" target="_blank" title="Cetak QR">
                                    <i class="bi bi-printer"></i> Cetak
                                </a>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard('{{ $meja->scan_url }}')" title="Salin Link">
                                    <i class="bi bi-clipboard"></i> Salin
                                </button>
                                <form action="{{ route('admin.meja.regenerate-token', $meja) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin buat ulang token QR? QR lama tidak akan berlaku lagi.')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning btn-sm" title="Generate ulang QR">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.meja.destroy', $meja) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus Meja {{ $meja->nomor_meja }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus Meja">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<!-- QR Code Generator Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($mejas as $meja)
            new QRCode(document.getElementById("qrcode-{{ $meja->id }}"), {
                text: "{{ $meja->scan_url }}",
                width: 180,
                height: 180,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        @endforeach
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show temporary toast notification
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast show align-items-center text-white bg-success border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-check-circle-fill me-2"></i>Link berhasil disalin!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }).catch(function() {
            // Fallback
            prompt('Salin link ini:', text);
        });
    }
</script>
@endpush
@endsection
