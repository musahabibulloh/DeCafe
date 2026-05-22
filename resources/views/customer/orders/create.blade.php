@extends('layouts.app')

@section('title', 'Pesan Menu - DeCafe')

@section('content')
<style>
    .qty-selector {
        display: flex;
        align-items: center;
        border: 1px solid var(--border-color);
        border-radius: 30px;
        background-color: var(--input-bg);
        overflow: hidden;
        padding: 2px;
        transition: border-color var(--transition-speed) ease;
    }

    .qty-selector:focus-within {
        border-color: var(--primary-color);
    }

    .qty-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: none;
        background-color: transparent;
        color: var(--text-main);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color var(--transition-speed) ease, transform 0.1s ease;
        outline: none;
    }

    .qty-btn:hover {
        background-color: rgba(var(--bs-body-color-rgb), 0.08);
    }

    .qty-btn:active {
        transform: scale(0.9);
    }

    .qty-input {
        width: 40px;
        border: none;
        background: transparent;
        text-align: center;
        color: var(--text-main);
        font-weight: 600;
        font-size: 0.95rem;
        outline: none;
        -moz-appearance: textfield;
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<div class="container-fluid">
    <h1 class="h3 mb-4">Pesan Menu</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customer.orders.store') }}" method="POST" id="orderForm" class="no-double-submit-prevent">
        @csrf

        <input type="hidden" name="nomor_meja" id="nomor_meja" value="-">

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Pilih Menu</h5>
            </div>
            <div class="card-body">
                @foreach($menus as $kategori => $items)
                    <h6 class="text-muted mb-3">{{ $kategori }}</h6>
                    <div class="row mb-4">
                        @foreach($items as $menu)
                            <div class="col-md-4 col-lg-3 mb-3">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">{{ $menu->nama_menu }}</h6>
                                        <p class="text-primary fw-bold mb-2">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                                        <div class="d-flex flex-column align-items-start gap-1">
                                            <input type="hidden" name="items[{{ $menu->id }}][menu_id]" value="{{ $menu->id }}">
                                            @if($menu->stok > 0)
                                                <div class="qty-selector">
                                                    <button class="qty-btn" type="button" onclick="decrementQty({{ $menu->id }})">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input type="number"
                                                           name="items[{{ $menu->id }}][jumlah]"
                                                           id="qty-input-{{ $menu->id }}"
                                                           class="qty-input"
                                                           min="0"
                                                           max="{{ $menu->stok }}"
                                                           value="0"
                                                           readonly
                                                           data-price="{{ $menu->harga }}"
                                                           data-name="{{ $menu->nama_menu }}"
                                                           data-index="{{ $menu->id }}"
                                                           onchange="updateItems()">
                                                    <button class="qty-btn" type="button" onclick="incrementQty({{ $menu->id }}, {{ $menu->stok }})">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="badge bg-danger">Habis</span>
                                            @endif
                                            <small class="text-muted mt-1">Stok: {{ $menu->stok }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Ringkasan Pesanan</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table -->
                <table class="table d-none d-md-table" id="summaryTable">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="summaryBody">
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada item dipilih</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th id="totalHarga">Rp 0</th>
                        </tr>
                    </tfoot>
                </table>

                <!-- Mobile List -->
                <div class="d-md-none">
                    <div class="list-group list-group-flush mb-3" id="summaryBodyMobile">
                        <div class="text-center text-muted py-4">Belum ada item dipilih</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold">Total:</span>
                        <h5 class="fw-bold text-success mb-0" id="totalHargaMobile">Rp 0</h5>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn" disabled>
                    Pesan Sekarang
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal Konfirmasi Pesanan -->
<div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px;">
            <div class="modal-header border-bottom" style="border-color: var(--border-color) !important;">
                <h5 class="modal-title" id="confirmOrderModalLabel">Konfirmasi Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: var(--theme-close-btn, invert(1));"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Pastikan pesanan sudah benar sebelum dikirim.</p>
                <!-- Meja ditentukan oleh Kasir saat pembayaran -->
                <div class="mb-3 border-bottom pb-2" style="border-color: var(--border-color) !important;">
                    <strong>Daftar Item:</strong>
                </div>
                <div class="mb-3" id="confirmItemsList" style="max-height: 250px; overflow-y: auto;">
                    <!-- Items summary will be inserted here -->
                </div>
                <div class="d-flex justify-content-between align-items-center pt-3 border-top" style="border-color: var(--border-color) !important;">
                    <span class="fw-bold">Total Pembayaran:</span>
                    <span class="fw-bold text-success h5 mb-0" id="confirmTotal">Rp 0</span>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-color) !important;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cek Lagi</button>
                <button type="button" class="btn btn-success" id="confirmSubmitBtn">Ya, Pesan Sekarang</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const menuItems = @json($menus->flatten()->keyBy('id'));

    function decrementQty(menuId) {
        const input = document.getElementById(`qty-input-${menuId}`);
        if (!input) return;
        let val = parseInt(input.value) || 0;
        if (val > 0) {
            input.value = val - 1;
            updateItems();
        }
    }

    function incrementQty(menuId, maxStok) {
        const input = document.getElementById(`qty-input-${menuId}`);
        if (!input) return;
        let val = parseInt(input.value) || 0;
        if (val < maxStok) {
            input.value = val + 1;
            updateItems();
        }
    }

    function updateItems() {
        const tbody = document.getElementById('summaryBody');
        const tbodyMobile = document.getElementById('summaryBodyMobile');
        const totalEl = document.getElementById('totalHarga');
        const totalElMobile = document.getElementById('totalHargaMobile');
        const submitBtn = document.getElementById('submitBtn');
        let rows = '';
        let rowsMobile = '';
        let total = 0;
        let hasItems = false;

        document.querySelectorAll('input[name^="items"]').forEach(input => {
            if (input.type === 'number') {
                const card = input.closest('.card');
                if (!card) return;
                const menuIdInput = card.querySelector('input[name*="[menu_id]"]');
                if (!menuIdInput) return;
                const menuId = menuIdInput.value;
                const qty = parseInt(input.value) || 0;
                const menu = menuItems[menuId];

                if (qty > 0 && menu) {
                    hasItems = true;
                    const subtotal = qty * menu.harga;
                    total += subtotal;
                    rows += `<tr>
                        <td>${menu.nama_menu}</td>
                        <td>${qty}</td>
                        <td>Rp ${menu.harga.toLocaleString('id-ID')}</td>
                        <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                    </tr>`;
                    rowsMobile += `<div class="list-group-item px-0 py-2 border-0 border-bottom bg-transparent" style="border-color: var(--border-color) !important;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold" style="color: var(--bs-body-color);">${menu.nama_menu}</span>
                            <span class="fw-bold text-primary">Rp ${subtotal.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-muted small">
                            <span>Rp ${menu.harga.toLocaleString('id-ID')}</span>
                            <span>Jumlah: <strong>${qty}x</strong></span>
                        </div>
                    </div>`;
                }
            }
        });

        if (!hasItems) {
            rows = '<tr><td colspan="4" class="text-center text-muted">Belum ada item dipilih</td></tr>';
            rowsMobile = '<div class="text-center text-muted py-4">Belum ada item dipilih</div>';
        }

        tbody.innerHTML = rows;
        tbodyMobile.innerHTML = rowsMobile;
        
        const formattedTotal = 'Rp ' + total.toLocaleString('id-ID');
        totalEl.textContent = formattedTotal;
        totalElMobile.textContent = formattedTotal;
        submitBtn.disabled = !hasItems;
    }

    let isConfirmed = false;
    const confirmModalEl = document.getElementById('confirmOrderModal');
    const confirmModal = new bootstrap.Modal(confirmModalEl);

    document.getElementById('orderForm').addEventListener('submit', function(e) {
        if (isConfirmed) return;

        e.preventDefault();

        const nomorMeja = document.getElementById('nomor_meja').value.trim();
        if (!nomorMeja) {
            alert('Silakan masukkan nomor meja.');
            return;
        }

        const inputs = document.querySelectorAll('input[name^="items"][type="number"]');
        let hasValidItem = false;
        let confirmItemsHtml = '';
        let total = 0;

        inputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                hasValidItem = true;
                const card = input.closest('.card');
                const menuIdInput = card.querySelector('input[name*="[menu_id]"]');
                const menuId = menuIdInput.value;
                const menu = menuItems[menuId];
                if (menu) {
                    const subtotal = qty * menu.harga;
                    total += subtotal;
                    confirmItemsHtml += `
                        <div class="d-flex justify-content-between py-2 border-bottom" style="border-color: var(--border-color) !important;">
                            <div>
                                <h6 class="mb-0 fw-semibold text-white">${menu.nama_menu}</h6>
                                <small class="text-muted">${qty} x Rp ${menu.harga.toLocaleString('id-ID')}</small>
                            </div>
                            <span class="fw-bold text-white">Rp ${subtotal.toLocaleString('id-ID')}</span>
                        </div>
                    `;
                }
            }
        });

        if (!hasValidItem) {
            alert('Pilih minimal 1 menu.');
            return;
        }

        document.getElementById('confirmItemsList').innerHTML = confirmItemsHtml;
        document.getElementById('confirmTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');

        confirmModal.show();
    });

    document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
        isConfirmed = true;
        this.disabled = true;
        this.textContent = 'Memproses Pesanan...';
        document.getElementById('orderForm').submit();
    });
</script>
@endpush
@endsection