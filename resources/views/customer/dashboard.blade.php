@extends('layouts.app')

@section('title', 'Dashboard - DeCafe')

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
        width: 32px;
        height: 32px;
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

    .menu-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid var(--border-color) !important;
    }

    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
        border-color: var(--primary-color) !important;
    }

    @media (min-width: 992px) {
        .sticky-sidebar {
            position: sticky;
            top: 1.5rem;
            max-height: calc(100vh - 3rem);
            overflow-y: auto;
        }
    }

    .customise-option-btn {
        color: var(--text-main) !important;
        border-color: var(--border-color) !important;
        background-color: var(--input-bg) !important;
        transition: all 0.2s ease !important;
    }
    .customise-option-btn:hover {
        background-color: var(--bg-card-hover) !important;
        border-color: var(--primary-color) !important;
        color: var(--primary-color) !important;
    }
    /* When checked */
    .btn-check:checked + .customise-option-btn {
        background: var(--primary-gradient) !important;
        border-color: transparent !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px var(--btn-primary-shadow) !important;
    }
</style>

<div class="container-fluid">
    <!-- Welcome Banner -->
    <div class="welcome-banner p-4 rounded-4 mb-4 shadow-sm" style="background: var(--primary-gradient); color: #fff;">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold mb-1">Halo, {{ auth()->user()->name }}! 👋</h2>
                <p class="mb-0 opacity-90">Nikmati menu makanan dan minuman terbaik dari DeCafe. Pesan langsung dari meja Anda.</p>
            </div>
            <div class="d-none d-sm-block fs-1 px-3">
                <i class="bi bi-cup-hot-fill"></i>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                <span class="input-group-text border-0" style="background-color: var(--input-bg) !important; border: 1px solid var(--border-color) !important; border-right: none !important; color: var(--text-muted);">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="menuSearch" class="form-control border-0" style="background-color: var(--input-bg) !important; border: 1px solid var(--border-color) !important; border-left: none !important; padding: 0.75rem 1rem !important;" placeholder="Cari makanan atau minuman..." onkeyup="filterMenus()">
            </div>
        </div>
    </div>

    <form action="{{ route('customer.orders.store') }}" method="POST" id="orderForm" class="no-double-submit-prevent">
        @csrf
        <input type="hidden" name="nomor_meja" id="hidden_nomor_meja">

        <div class="row g-4">
            <!-- Left Side: Menus -->
            <div class="col-lg-8">
                @foreach($menus as $kategori => $items)
                    <div class="mb-4 category-section">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <h4 class="fw-bold mb-0 text-capitalize">{{ $kategori }}</h4>
                            <span class="badge bg-secondary rounded-pill category-count">{{ count($items) }} Menu</span>
                        </div>
                        <div class="row g-3">
                            @foreach($items as $menu)
                                <div class="col-md-6 col-xl-4 menu-item-col" data-name="{{ strtolower($menu->nama_menu) }}" data-desc="{{ strtolower($menu->deskripsi ?? '') }}">
                                    <div class="card h-100 menu-card overflow-hidden">
                                        @if($menu->gambar)
                                            <img src="{{ asset('storage/' . $menu->gambar) }}" class="card-img-top" alt="{{ $menu->nama_menu }}" style="height: 160px; object-fit: cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center bg-secondary-subtle text-muted" style="height: 160px; background-color: rgba(255,255,255,0.02) !important;">
                                                <i class="bi bi-cup-straw fs-1 opacity-50" style="color: var(--text-muted);"></i>
                                            </div>
                                        @endif
                                        <div class="card-body d-flex flex-column justify-content-between p-3">
                                            <div>
                                                <h6 class="card-title fw-bold mb-1 text-truncate">{{ $menu->nama_menu }}</h6>
                                                <p class="small text-muted mb-2 text-truncate" title="{{ $menu->deskripsi ?? 'Menu nikmat dari DeCafe' }}">
                                                    {{ $menu->deskripsi ?? 'Pilihan hidangan berkualitas tinggi' }}
                                                </p>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="text-primary fw-bold fs-6">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                                                    <small class="text-muted">Stok: {{ $menu->stok }}</small>
                                                </div>

                                                <input type="hidden" name="items[{{ $menu->id }}][menu_id]" value="{{ $menu->id }}">
                                                <input type="hidden" name="items[{{ $menu->id }}][catatan_item]" id="catatan-{{ $menu->id }}">
                                                @if($menu->stok > 0)
                                                    <div class="qty-selector w-100">
                                                        <button class="qty-btn" type="button" onclick="decrementQty({{ $menu->id }})">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input type="number"
                                                               name="items[{{ $menu->id }}][jumlah]"
                                                               id="qty-input-{{ $menu->id }}"
                                                               class="qty-input flex-grow-1"
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
                                                    <span class="badge bg-danger w-100 py-2">Habis</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div id="noResultsAlert" class="alert alert-info text-center py-4" style="display: none !important;">
                    <i class="bi bi-search fs-2 mb-2 d-block"></i>
                    Menu yang Anda cari tidak ditemukan. Coba ketik kata kunci lain!
                </div>

                @if($menus->isEmpty())
                    <div class="alert alert-info">Belum ada menu tersedia saat ini. Silakan hubungi kasir or pelayan.</div>
                @endif

                <!-- Spacer for mobile view to prevent floating cart bar from overlapping menu items -->
                <div style="height: 100px;" class="d-lg-none"></div>
            </div>

            <!-- Right Side: Order Summary & Info (Sticky Sidebar) -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <!-- Cart Summary Card -->
                    <div class="card border-0 shadow-sm d-none d-lg-block">
                        <div class="card-header bg-dark text-white py-3">
                            <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-cart-fill"></i> Ringkasan Pesanan
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div id="summaryContainer" style="max-height: 250px; overflow-y: auto;">
                                <div class="list-group list-group-flush" id="summaryBody">
                                    <div class="text-center text-muted py-4">Belum ada item dipilih</div>
                                </div>
                            </div>

                            <div class="p-3 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold">Total Pembayaran:</span>
                                    <h4 class="text-primary fw-bold mb-0" id="totalHarga">Rp 0</h4>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" id="submitBtn" disabled>
                                    <i class="bi bi-send-fill"></i> Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Mobile Bottom Bar -->
        <div id="mobileCartBar" class="d-lg-none position-fixed bottom-0 start-0 end-0 border-top shadow-lg p-3" style="z-index: 1030; display: none; background-color: var(--bs-body-bg) !important; border-top: 1px solid rgba(var(--bs-body-color-rgb), 0.15) !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block" id="mobileTotalItems">0 Item</small>
                    <h5 class="fw-bold text-primary mb-0" id="mobileTotalHarga">Rp 0</h5>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#mobileCartDetails">
                        Detail <i class="bi bi-chevron-up"></i>
                    </button>
                    <button type="submit" class="btn btn-success fw-bold px-4" id="mobileSubmitBtn" disabled>
                        Pesan <i class="bi bi-send-fill ms-1"></i>
                    </button>
                </div>
            </div>
            
            <!-- Collapsible list of items on mobile -->
            <div class="collapse mt-3" id="mobileCartDetails">
                <hr class="my-2" style="color: var(--bs-body-color);">
                <div id="mobileCartList" class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                    <!-- Filled dynamically by updateItems() -->
                </div>
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
                <p class="text-muted mb-3">Rincian pesanan yang akan dibuat:</p>
                <div id="modalOrderItems" class="list-group list-group-flush mb-4" style="max-height: 200px; overflow-y: auto;">
                    <!-- Filled dynamically -->
                </div>
                
                <div class="mb-3">
                    <label for="modal_nomor_meja" class="form-label fw-bold">Nomor Meja Anda <span class="text-danger">*</span></label>
                    <input type="text" id="modal_nomor_meja" class="form-control" placeholder="Contoh: 05" required>
                    <div class="invalid-feedback" id="modalMejaError" style="display: none;">Nomor meja wajib diisi.</div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top" style="border-color: var(--border-color) !important;">
                    <span class="text-muted">Total Pembayaran:</span>
                    <h4 class="text-primary fw-bold mb-0" id="modalTotalHarga">Rp 0</h4>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-color) !important;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnConfirmOrderSubmit">Konfirmasi & Pesan</button>
            </div>
        </div>
</div>
</div>

<!-- Modal Kustomisasi Menu -->
<div class="modal fade" id="customiseNasiBakarModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="customiseNasiBakarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px;">
            <div class="modal-header border-bottom" style="border-color: var(--border-color) !important;">
                <h5 class="modal-title" id="customiseNasiBakarModalLabel">Kustomisasi Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: var(--theme-close-btn, invert(1));"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div class="alert alert-info py-2" style="background-color: rgba(13, 202, 240, 0.1); border-color: rgba(13, 202, 240, 0.2); color: #0dcaf0;">
                    Silakan tentukan pilihan rasa untuk <strong id="customisePorsiTitle">Porsi #1</strong>.
                </div>
                
                <form id="customiseNasiBakarForm">

                    <div class="mb-4" id="laukOptionsGroup">
                        <label class="form-label fw-bold mb-2">
                            Pilih Jenis Lauk <span class="text-danger" id="laukRequiredMark">*</span>
                            <span class="small text-muted fw-normal" id="laukLimitHint">(Maksimal 1)</span>
                        </label>
                        <div class="row g-2" id="laukOptions"></div>
                    </div>

                    <div class="mb-4" id="sambalOptionsGroup">
                        <label class="form-label fw-bold mb-2">
                            Pilih Jenis Sambal <span class="text-danger" id="sambalRequiredMark">*</span>
                        </label>
                        <div class="row g-2" id="sambalOptions"></div>
                    </div>

                    <div class="mb-3" id="ekstraOptionsGroup">
                        <label class="form-label fw-bold mb-2">Pilih Ekstra Lauk (Opsional)</label>
                        <div class="row g-2" id="ekstraOptions"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-color) !important;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSaveCustomisation">Simpan Porsi</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const menuItems = @json($menus->flatten()->keyBy('id'));
    let menuCustomizations = {};
    let activeCustomiseMenuId = null;
    let activeCustomiseMaxStok = 0;
    
    const customiseModal = new bootstrap.Modal(document.getElementById('customiseNasiBakarModal'));

    document.getElementById('customiseNasiBakarForm').addEventListener('change', function(event) {
        if (event.target.name !== 'jenis_lauk' || !activeCustomiseMenuId) return;

        const activeMenu = menuItems[activeCustomiseMenuId];
        const maxAllowed = parseInt(activeMenu.maksimal_lauk) || 1;
        const checked = Array.from(document.querySelectorAll('input[name="jenis_lauk"]:checked'));

        if (checked.length > maxAllowed) {
            if (maxAllowed === 1) {
                checked.forEach(otherCb => {
                    if (otherCb !== event.target) otherCb.checked = false;
                });
            } else {
                event.target.checked = false;
                alert(`Pilihan lauk maksimal ${maxAllowed}.`);
            }
        }
    });

    function hasCustomOptions(menu) {
        return hasItems(menu.options);
    }

    function hasItems(value) {
        return Array.isArray(value) && value.length > 0;
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function optionsByType(menu, type) {
        return Array.isArray(menu.options)
            ? menu.options.filter(option => option.tipe === type)
            : [];
    }

    function renderOptionButtons(containerId, name, options, type, icon = '') {
        const container = document.getElementById(containerId);
        container.innerHTML = options.map((option, index) => {
            const id = `${name}_${index}`;
            const isAvailable = option.status === 'tersedia';
            const firstAvailableIndex = options.findIndex(item => item.status === 'tersedia');
            const checked = type === 'radio' && index === firstAvailableIndex ? 'checked' : '';
            const disabled = isAvailable ? '' : 'disabled';
            const statusLabel = isAvailable ? '' : '<span class="badge bg-danger ms-2">Habis</span>';
            return `
                <div class="col-sm-6 col-md-4">
                    <input type="${type}" class="btn-check" name="${name}" id="${id}" value="${escapeHtml(option.nama_opsi)}" ${checked} ${disabled}>
                    <label class="btn customise-option-btn w-100 text-start py-2 px-3 text-truncate" for="${id}">
                        ${icon} ${escapeHtml(option.nama_opsi)} ${statusLabel}
                    </label>
                </div>
            `;
        }).join('');
    }

    function prepareCustomiseModal(menu, nextPortionNumber) {
        const laukOptions = optionsByType(menu, 'lauk');
        const sambalOptions = optionsByType(menu, 'sambal');
        const ekstraOptions = optionsByType(menu, 'ekstra_lauk');
        const maxLauk = parseInt(menu.maksimal_lauk) || 1;

        document.getElementById('customiseNasiBakarModalLabel').textContent = 'Kustomisasi ' + menu.nama_menu;
        document.getElementById('customisePorsiTitle').textContent = `Porsi #${nextPortionNumber}`;
        document.getElementById('laukLimitHint').textContent = `(Maksimal ${maxLauk})`;
        document.getElementById('laukRequiredMark').style.display = menu.wajib_pilih_lauk ? '' : 'none';
        document.getElementById('sambalRequiredMark').style.display = menu.wajib_pilih_sambal ? '' : 'none';

        document.getElementById('customiseNasiBakarForm').reset();
        document.getElementById('laukOptionsGroup').style.display = hasItems(laukOptions) ? '' : 'none';
        document.getElementById('sambalOptionsGroup').style.display = hasItems(sambalOptions) ? '' : 'none';
        document.getElementById('ekstraOptionsGroup').style.display = hasItems(ekstraOptions) ? '' : 'none';

        renderOptionButtons('laukOptions', 'jenis_lauk', laukOptions, 'checkbox');
        renderOptionButtons('sambalOptions', 'jenis_sambal', sambalOptions, 'radio');
        renderOptionButtons('ekstraOptions', 'ekstra_lauk', ekstraOptions, 'checkbox', '<i class="bi bi-plus-lg me-1 small"></i>');
    }

    function decrementQty(menuId) {
        const input = document.getElementById(`qty-input-${menuId}`);
        if (!input) return;
        let val = parseInt(input.value) || 0;
        if (val > 0) {
            const menu = menuItems[menuId];
            const needsCustomOptions = menu && hasCustomOptions(menu);
            
            if (needsCustomOptions && menuCustomizations[menuId]) {
                menuCustomizations[menuId].pop();
                document.getElementById(`catatan-${menuId}`).value = menuCustomizations[menuId].join(' \n ');
            }

            input.value = val - 1;
            updateItems();
        }
    }

    function incrementQty(menuId, maxStok) {
        const input = document.getElementById(`qty-input-${menuId}`);
        if (!input) return;
        let val = parseInt(input.value) || 0;
        
        const menu = menuItems[menuId];
        const needsCustomOptions = menu && hasCustomOptions(menu);

        if (needsCustomOptions) {
            if (val >= maxStok) return;
            activeCustomiseMenuId = menuId;
            activeCustomiseMaxStok = maxStok;
            prepareCustomiseModal(menu, val + 1);
            customiseModal.show();
        } else {
            if (val < maxStok) {
                input.value = val + 1;
                updateItems();
            }
        }
    }

    document.getElementById('btnSaveCustomisation').addEventListener('click', function() {
        if (!activeCustomiseMenuId) return;

        const form = document.getElementById('customiseNasiBakarForm');
        const activeMenu = menuItems[activeCustomiseMenuId];
        
        // Get selected lauk values
        let selectedLauk = [];
        form.querySelectorAll('input[name="jenis_lauk"]:checked').forEach(cb => {
            selectedLauk.push(cb.value);
        });
        
        if (activeMenu.wajib_pilih_lauk && selectedLauk.length === 0) {
            alert('Silakan pilih minimal 1 jenis lauk.');
            return;
        }

        // Get selected sambal
        const selectedSambal = form.querySelector('input[name="jenis_sambal"]:checked');
        if (activeMenu.wajib_pilih_sambal && !selectedSambal) {
            alert('Silakan pilih jenis sambal.');
            return;
        }
        const jenisSambal = selectedSambal ? selectedSambal.value : 'Tidak ada';
        
        // Get checked extra options
        let ekstraLauk = [];
        form.querySelectorAll('input[name="ekstra_lauk"]:checked').forEach(cb => {
            ekstraLauk.push(cb.value);
        });
        
        // Format extra options list
        const ekstraStr = ekstraLauk.length > 0 ? ekstraLauk.join(', ') : 'Tidak ada';

        // Format customization details
        const qtyInput = document.getElementById(`qty-input-${activeCustomiseMenuId}`);
        const currentVal = parseInt(qtyInput.value) || 0;
        const porsiNum = currentVal + 1;

        const laukStr = selectedLauk.length > 0 ? selectedLauk.join(', ') : 'Tidak ada';
        const customizationText = `Porsi #${porsiNum} [Lauk: ${laukStr} | Sambal: ${jenisSambal} | Ekstra: ${ekstraStr}]`;

        if (!menuCustomizations[activeCustomiseMenuId]) {
            menuCustomizations[activeCustomiseMenuId] = [];
        }
        menuCustomizations[activeCustomiseMenuId].push(customizationText);

        // Update hidden input
        document.getElementById(`catatan-${activeCustomiseMenuId}`).value = menuCustomizations[activeCustomiseMenuId].join(' \n ');

        // Increment input value
        qtyInput.value = porsiNum;

        // Close modal and update ui
        customiseModal.hide();
        updateItems();

        // Reset variables
        activeCustomiseMenuId = null;
        activeCustomiseMaxStok = 0;
    });

    function updateItems() {
        const summaryBody = document.getElementById('summaryBody');
        const totalEl = document.getElementById('totalHarga');
        const submitBtn = document.getElementById('submitBtn');

        // Mobile elements
        const mobileCartBar = document.getElementById('mobileCartBar');
        const mobileTotalItems = document.getElementById('mobileTotalItems');
        const mobileTotalHarga = document.getElementById('mobileTotalHarga');
        const mobileCartList = document.getElementById('mobileCartList');
        const mobileSubmitBtn = document.getElementById('mobileSubmitBtn');

        let htmlContent = '';
        let total = 0;
        let totalQty = 0;
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

                // Get customization text
                const catatanInput = card.querySelector('input[id^="catatan-"]');
                const catatan = catatanInput ? catatanInput.value : '';

                if (qty > 0 && menu) {
                    hasItems = true;
                    const subtotal = qty * menu.harga;
                    total += subtotal;
                    totalQty += qty;

                    let catatanHtml = '';
                    if (catatan) {
                        const lines = catatan.split(' \n ');
                        catatanHtml = '<div class="mt-1 small text-muted font-monospace" style="font-size: 0.72rem; line-height: 1.3;">';
                        lines.forEach(line => {
                            catatanHtml += `<div class="text-truncate" title="${line}"><i class="bi bi-gear-wide-connected me-1"></i> ${line}</div>`;
                        });
                        catatanHtml += '</div>';
                    }

                    htmlContent += `<div class="list-group-item px-3 py-2 bg-transparent border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div style="flex-grow: 1; min-width: 0;">
                                <span class="fw-semibold text-truncate d-block" style="max-width: 180px; color: var(--bs-body-color);">${menu.nama_menu}</span>
                                <small class="text-muted">Rp ${menu.harga.toLocaleString('id-ID')} x ${qty}</small>
                                ${catatanHtml}
                            </div>
                            <span class="fw-bold text-primary ms-2">Rp ${subtotal.toLocaleString('id-ID')}</span>
                        </div>
                    </div>`;
                }
            }
        });

        if (!hasItems) {
            htmlContent = '<div class="text-center text-muted py-4">Belum ada item dipilih</div>';
        }

        // Update Desktop
        summaryBody.innerHTML = htmlContent;
        totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
        submitBtn.disabled = !hasItems;

        // Update Mobile
        if (mobileCartBar) {
            if (hasItems) {
                mobileCartBar.style.setProperty('display', 'block', 'important');
                mobileTotalItems.textContent = `${totalQty} Item dipilih`;
                mobileTotalHarga.textContent = 'Rp ' + total.toLocaleString('id-ID');
                mobileCartList.innerHTML = htmlContent;
                mobileSubmitBtn.disabled = false;
            } else {
                mobileCartBar.style.setProperty('display', 'none', 'important');
                mobileSubmitBtn.disabled = true;
            }
        }
    }

    let isOrderConfirmed = false;
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmOrderModal'));
    const orderForm = document.getElementById('orderForm');

    orderForm.addEventListener('submit', function(e) {
        if (isOrderConfirmed) return;

        e.preventDefault();

        // Populate Modal Items
        const modalOrderItems = document.getElementById('modalOrderItems');
        let htmlContent = '';
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

                const catatanInput = card.querySelector('input[id^="catatan-"]');
                const catatan = catatanInput ? catatanInput.value : '';

                if (qty > 0 && menu) {
                    hasItems = true;
                    const subtotal = qty * menu.harga;
                    total += subtotal;

                    let catatanHtml = '';
                    if (catatan) {
                        const lines = catatan.split(' \n ');
                        catatanHtml = '<div class="mt-1 small text-muted font-monospace" style="font-size: 0.72rem; line-height: 1.3;">';
                        lines.forEach(line => {
                            catatanHtml += `<div class="text-truncate" title="${line}"><i class="bi bi-gear-wide-connected me-1"></i> ${line}</div>`;
                        });
                        catatanHtml += '</div>';
                    }

                    htmlContent += `<div class="list-group-item px-0 py-2 bg-transparent border-0 border-bottom d-flex justify-content-between align-items-start">
                        <div style="flex-grow: 1; min-width: 0;">
                            <span class="fw-semibold d-block text-truncate">${menu.nama_menu}</span>
                            <small class="text-muted d-block">${qty}x @ Rp ${menu.harga.toLocaleString('id-ID')}</small>
                            ${catatanHtml}
                        </div>
                        <strong class="text-primary ms-2">Rp ${subtotal.toLocaleString('id-ID')}</strong>
                    </div>`;
                }
            }
        });

        if (!hasItems) {
            alert('Silakan pilih minimal 1 menu sebelum melakukan pemesanan.');
            return;
        }

        modalOrderItems.innerHTML = htmlContent;
        document.getElementById('modalTotalHarga').textContent = 'Rp ' + total.toLocaleString('id-ID');
        
        // Clear errors
        document.getElementById('modal_nomor_meja').classList.remove('is-invalid');
        document.getElementById('modalMejaError').style.display = 'none';

        confirmModal.show();
    });

    document.getElementById('btnConfirmOrderSubmit').addEventListener('click', function() {
        const mejaVal = document.getElementById('modal_nomor_meja').value.trim();
        if (!mejaVal) {
            document.getElementById('modal_nomor_meja').classList.add('is-invalid');
            document.getElementById('modalMejaError').style.display = 'block';
            return;
        }

        // Set hidden input value
        document.getElementById('hidden_nomor_meja').value = mejaVal;

        // Submit form
        isOrderConfirmed = true;
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
        orderForm.submit();
    });

    function filterMenus() {
        const query = document.getElementById('menuSearch').value.toLowerCase().trim();
        const categorySections = document.querySelectorAll('.category-section');

        categorySections.forEach(section => {
            const items = section.querySelectorAll('.menu-item-col');
            let visibleCount = 0;

            items.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const desc = item.getAttribute('data-desc') || '';
                
                if (name.includes(query) || desc.includes(query)) {
                    item.style.setProperty('display', 'block', 'important');
                    visibleCount++;
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });

            // Update badge count
            const countBadge = section.querySelector('.category-count');
            if (countBadge) {
                countBadge.textContent = `${visibleCount} Menu`;
            }

            // Hide or show the whole category section
            if (visibleCount === 0) {
                section.style.setProperty('display', 'none', 'important');
            } else {
                section.style.setProperty('display', 'block', 'important');
            }
        });

        // Show "no menus found" alert if all sections are hidden
        const noResultsAlert = document.getElementById('noResultsAlert');
        const anyVisible = Array.from(categorySections).some(section => section.style.display !== 'none');
        if (noResultsAlert) {
            if (!anyVisible && query !== '') {
                noResultsAlert.style.setProperty('display', 'block', 'important');
            } else {
                noResultsAlert.style.setProperty('display', 'none', 'important');
            }
        }
    }
</script>
@endpush
@endsection
