@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Pembayaran Pesanan</h2>
        <a href="{{ route('kasir.orders.show', $order) }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Kode Pesanan:</strong> {{ $order->kode_pesanan }}</p>
            <p><strong>Nomor Meja:</strong> {{ $order->nomor_meja }}</p>
            <p><strong>Total Bayar:</strong> Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>

            @if ($order->bukti_pembayaran)
                <div class="mt-3 pt-3 border-top">
                    <h6 class="fw-bold mb-2 text-warning"><i class="bi bi-receipt"></i> Bukti Pembayaran QRIS:</h6>
                    <div style="max-width: 250px;">
                        <a href="{{ asset($order->bukti_pembayaran) }}" target="_blank" class="d-block border rounded p-2 text-center bg-light text-decoration-none">
                            <img src="{{ asset($order->bukti_pembayaran) }}" class="img-fluid rounded" style="max-height: 180px;" alt="Bukti Pembayaran">
                            <span class="d-block small text-muted mt-2"><i class="bi bi-zoom-in"></i> Klik untuk memperbesar</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
             <form action="{{ route('kasir.payments.store', $order) }}" method="POST" id="paymentForm" class="no-double-submit-prevent">
                 @csrf
                 <div class="mb-3">
                     <label class="form-label">Nomor Meja</label>
                     <input type="text" name="nomor_meja" class="form-control" value="{{ old('nomor_meja', $order->nomor_meja === '-' ? '' : $order->nomor_meja) }}" required placeholder="Masukkan nomor meja (misal: 05)">
                     @error('nomor_meja')
                         <small class="text-danger">{{ $message }}</small>
                     @enderror
                 </div>
                 <div class="mb-3">
                     <label class="form-label">Metode Pembayaran</label>
                     <select name="metode_pembayaran" class="form-select" required>
                         <option value="">Pilih Metode</option>
                         <option value="tunai" @selected(old('metode_pembayaran') === 'tunai')>Tunai</option>
                         <option value="qris" @selected(old('metode_pembayaran', $order->status_pembayaran === 'menunggu_konfirmasi' ? 'qris' : '') === 'qris')>QRIS</option>
                         <option value="transfer" @selected(old('metode_pembayaran') === 'transfer')>Transfer</option>
                         <option value="ewallet" @selected(old('metode_pembayaran') === 'ewallet')>E-Wallet</option>
                     </select>
                     @error('metode_pembayaran')
                         <small class="text-danger">{{ $message }}</small>
                     @enderror
                 </div>
                 <div class="mb-3">
                     <label class="form-label">Uang Diterima (Tunai)</label>
                     <input type="number" name="uang_diterima" class="form-control" value="{{ old('uang_diterima') }}" min="0">
                     @error('uang_diterima')
                         <small class="text-danger">{{ $message }}</small>
                     @enderror
                 </div>
                 <button type="submit" class="btn btn-success w-100">Konfirmasi Pembayaran</button>
             </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Pembayaran -->
    <div class="modal fade" id="confirmPaymentModal" tabindex="-1" aria-labelledby="confirmPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px;">
                <div class="modal-header border-bottom" style="border-color: var(--border-color) !important;">
                    <h5 class="modal-title" id="confirmPaymentModalLabel">Konfirmasi Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: var(--theme-close-btn, invert(1));"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Pastikan rincian pembayaran sudah sesuai.</p>
                    <div class="d-flex flex-column gap-2 mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Tagihan:</span>
                            <strong class="text-white">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Metode Pembayaran:</span>
                            <strong class="text-white" id="confirmMetode"></strong>
                        </div>
                        <div class="d-flex justify-content-between" id="confirmUangDiterimaRow">
                            <span class="text-muted">Uang Diterima:</span>
                            <strong class="text-white" id="confirmUangDiterima"></strong>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2" id="confirmKembalianRow" style="border-color: var(--border-color) !important;">
                            <span class="fw-bold">Kembalian:</span>
                            <strong class="text-success h5 mb-0" id="confirmKembalian">Rp 0</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top" style="border-color: var(--border-color) !important;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="btnConfirmSubmit">Konfirmasi & Simpan</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let isConfirmed = false;
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmPaymentModal'));
        const totalHarga = {{ $order->total_harga }};
        const paymentForm = document.getElementById('paymentForm');

        paymentForm.addEventListener('submit', function(e) {
            if (isConfirmed) return;

            e.preventDefault();

            const metode = paymentForm.metode_pembayaran.value;
            const uangInput = paymentForm.uang_diterima.value;
            const nomorMeja = paymentForm.nomor_meja.value.trim();

            if (!nomorMeja) {
                alert('Masukkan nomor meja.');
                return;
            }

            if (!metode) {
                alert('Pilih metode pembayaran.');
                return;
            }

            if (metode === 'tunai') {
                const uang = parseInt(uangInput) || 0;
                if (uang < totalHarga) {
                    alert('Uang diterima kurang dari total tagihan.');
                    return;
                }
                document.getElementById('confirmUangDiterimaRow').classList.remove('d-none');
                document.getElementById('confirmKembalianRow').classList.remove('d-none');
                document.getElementById('confirmUangDiterima').textContent = 'Rp ' + uang.toLocaleString('id-ID');
                document.getElementById('confirmKembalian').textContent = 'Rp ' + (uang - totalHarga).toLocaleString('id-ID');
            } else {
                document.getElementById('confirmUangDiterimaRow').classList.add('d-none');
                document.getElementById('confirmKembalianRow').classList.add('d-none');
            }

            document.getElementById('confirmMetode').textContent = metode.toUpperCase();

            confirmModal.show();
        });

        document.getElementById('btnConfirmSubmit').addEventListener('click', function() {
            isConfirmed = true;
            this.disabled = true;
            this.textContent = 'Menyimpan...';
            paymentForm.submit();
        });
    </script>
    @endpush
@endsection
