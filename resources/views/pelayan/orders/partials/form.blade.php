<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nomor Meja</label>
        <input type="text" name="nomor_meja" class="form-control" value="{{ old('nomor_meja', $order->nomor_meja ?? '') }}" required placeholder="Contoh: 05">
        @error('nomor_meja')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Atas Nama (Pemesan) <span class="text-danger">*</span></label>
        <input type="text" name="atas_nama" class="form-control" value="{{ old('atas_nama', $order->atas_nama ?? '') }}" placeholder="Nama pemesan" required>
        @error('atas_nama')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Nama Pelanggan (User Akun)</label>
        <input type="text" name="nama_pelanggan" class="form-control" value="{{ old('nama_pelanggan', $order->nama_pelanggan ?? '') }}" placeholder="Nama pelanggan (opsional)">
        @error('nama_pelanggan')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Tipe Pesanan <span class="text-danger">*</span></label>
        <div class="d-flex gap-3 pt-2">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe_pesanan" id="tipe_dine_in" value="dine_in" {{ old('tipe_pesanan', $order->tipe_pesanan ?? 'dine_in') === 'dine_in' ? 'checked' : '' }}>
                <label class="form-check-label" for="tipe_dine_in">
                    Dine In (Makan di Tempat)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe_pesanan" id="tipe_take_away" value="take_away" {{ old('tipe_pesanan', $order->tipe_pesanan ?? '') === 'take_away' ? 'checked' : '' }}>
                <label class="form-check-label" for="tipe_take_away">
                    Take Away (Bungkus)
                </label>
            </div>
        </div>
        @error('tipe_pesanan')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label">Catatan Pesanan</label>
        <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan khusus, misal: tidak pedas, dll.">{{ old('catatan', $order->catatan ?? '') }}</textarea>
    </div>
</div>

<hr class="my-4">

<h5 class="mb-3">Pilih Menu & Jumlah</h5>

<!-- Desktop View -->
<div class="d-none d-md-block table-responsive">
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>Menu</th>
                <th>Harga</th>
                <th>Stok</th>
                <th width="120">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($menus as $index => $menu)
                @php
                    $existing = $itemsMap[$menu->id] ?? null;
                @endphp
                <tr>
                    <td>
                        <span class="fw-semibold">{{ $menu->nama_menu }}</span>
                        <input type="hidden" name="items[{{ $index }}][menu_id]" value="{{ $menu->id }}">
                    </td>
                    <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                    <td>
                        @if($menu->stok > 0)
                            <span class="badge bg-success">{{ $menu->stok }}</span>
                        @else
                            <span class="badge bg-danger">Habis</span>
                        @endif
                    </td>
                    <td>
                        <input type="number" 
                               name="items[{{ $index }}][jumlah]" 
                               class="form-control qty-input" 
                               min="0" 
                               max="{{ $menu->stok }}" 
                               data-index="{{ $index }}" 
                               value="{{ old('items.' . $index . '.jumlah', $existing->jumlah ?? 0) }}"
                               @if($menu->stok == 0) disabled placeholder="Habis" @endif>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Mobile View -->
<div class="d-md-none">
    <div class="row g-2">
        @foreach ($menus as $index => $menu)
            @php
                $existing = $itemsMap[$menu->id] ?? null;
            @endphp
            <div class="col-12">
                <div class="card shadow-sm p-3" style="background-color: var(--bs-card-bg); border: 1px solid var(--border-color) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-primary">{{ $menu->nama_menu }}</span>
                        @if($menu->stok > 0)
                            <span class="badge bg-secondary">Stok: {{ $menu->stok }}</span>
                        @else
                            <span class="badge bg-danger">Habis</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-success fw-semibold">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                        <div style="width: 100px;">
                            <input type="number" 
                                   name="items_mobile[{{ $index }}][jumlah]" 
                                   class="form-control form-control-sm text-center qty-input-mobile" 
                                   min="0" 
                                   max="{{ $menu->stok }}" 
                                   data-index="{{ $index }}" 
                                   value="{{ old('items.' . $index . '.jumlah', $existing->jumlah ?? 0) }}" 
                                   placeholder="0"
                                   @if($menu->stok == 0) disabled @endif>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qtyInputs = document.querySelectorAll('.qty-input');
        const qtyInputsMobile = document.querySelectorAll('.qty-input-mobile');

        // Sync Desktop Input to Mobile and validate
        qtyInputs.forEach(input => {
            input.addEventListener('input', function() {
                const index = this.getAttribute('data-index');
                const val = parseInt(this.value) || 0;
                const max = parseInt(this.getAttribute('max')) || 0;

                if (val > max) {
                    alert('Jumlah melebihi stok yang tersedia (' + max + ').');
                    this.value = max;
                    updateMobileInput(index, max);
                    return;
                }
                if (val < 0) {
                    this.value = 0;
                    updateMobileInput(index, 0);
                    return;
                }
                updateMobileInput(index, val);
            });
        });

        // Sync Mobile Input to Desktop and validate
        qtyInputsMobile.forEach(input => {
            input.addEventListener('input', function() {
                const index = this.getAttribute('data-index');
                const val = parseInt(this.value) || 0;
                const max = parseInt(this.getAttribute('max')) || 0;

                if (val > max) {
                    alert('Jumlah melebihi stok yang tersedia (' + max + ').');
                    this.value = max;
                    updateDesktopInput(index, max);
                    return;
                }
                if (val < 0) {
                    this.value = 0;
                    updateDesktopInput(index, 0);
                    return;
                }
                updateDesktopInput(index, val);
            });
        });

        function updateMobileInput(index, val) {
            const mobileInput = document.querySelector(`.qty-input-mobile[data-index="${index}"]`);
            if (mobileInput) {
                mobileInput.value = val;
            }
        }

        function updateDesktopInput(index, val) {
            const desktopInput = document.querySelector(`.qty-input[data-index="${index}"]`);
            if (desktopInput) {
                desktopInput.value = val;
            }
        }
    });
</script>
@endpush
