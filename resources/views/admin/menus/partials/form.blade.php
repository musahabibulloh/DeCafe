<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Menu</label>
        <input type="text" name="nama_menu" class="form-control" value="{{ old('nama_menu', $menu->nama_menu ?? '') }}" required>
        @error('nama_menu')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Kategori</label>
        <select name="kategori" class="form-select" required>
            <option value="">Pilih Kategori</option>
            <option value="makanan" @selected(old('kategori', $menu->kategori ?? '') === 'makanan')>Makanan</option>
            <option value="minuman" @selected(old('kategori', $menu->kategori ?? '') === 'minuman')>Minuman</option>
        </select>
        @error('kategori')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Harga</label>
        <input type="number" name="harga" class="form-control" value="{{ old('harga', $menu->harga ?? '') }}" min="0" required>
        @error('harga')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Stok</label>
        <input type="number" name="stok" class="form-control" value="{{ old('stok', $menu->stok ?? 0) }}" min="0" required>
        @error('stok')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="tersedia" @selected(old('status', $menu->status ?? 'tersedia') === 'tersedia')>Tersedia</option>
            <option value="habis" @selected(old('status', $menu->status ?? '') === 'habis')>Habis</option>
            <option value="nonaktif" @selected(old('status', $menu->status ?? '') === 'nonaktif')>Nonaktif</option>
        </select>
        @error('status')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Gambar</label>
        <input type="file" name="gambar" class="form-control">
        @error('gambar')
            <small class="text-danger">{{ $message }}</small>
        @enderror
        @if (!empty($menu?->gambar))
            <small class="text-muted">Gambar saat ini: {{ $menu->gambar }}</small>
        @endif
    </div>
    <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $menu->deskripsi ?? '') }}</textarea>
    </div>
</div>

<hr class="my-4">

@php
    $optionRows = old('options');

    if ($optionRows === null) {
        $optionRows = $menu?->options
            ? $menu->options->map(fn ($option) => [
                'nama_opsi' => $option->nama_opsi,
                'tipe' => $option->tipe,
                'status' => $option->status,
            ])->values()->all()
            : [];
    }
@endphp

<div class="d-flex justify-content-between align-items-start gap-3 mb-3">
    <div>
        <h5 class="mb-1">Opsi Tambahan</h5>
        <p class="text-muted small mb-0">Tambahkan opsi yang boleh dipilih customer. Kosongkan jika menu tidak perlu tambahan.</p>
    </div>
    <button type="button" class="btn btn-outline-primary btn-sm" id="addOptionRow">
        <i class="bi bi-plus-lg"></i> Tambah Opsi
    </button>
</div>

<div class="row g-3">
    <div class="col-12">
        <div class="table-responsive">
            <table class="table align-middle mb-2">
                <thead>
                    <tr>
                        <th>Nama Opsi</th>
                        <th style="width: 180px;">Jenis</th>
                        <th style="width: 160px;">Status</th>
                        <th style="width: 70px;"></th>
                    </tr>
                </thead>
                <tbody id="optionRows">
                    @forelse($optionRows as $index => $option)
                        <tr>
                            <td>
                                <input type="text" name="options[{{ $index }}][nama_opsi]" class="form-control" value="{{ $option['nama_opsi'] ?? '' }}" placeholder="Contoh: Ayam suwir">
                            </td>
                            <td>
                                <select name="options[{{ $index }}][tipe]" class="form-select">
                                    <option value="lauk" @selected(($option['tipe'] ?? 'lauk') === 'lauk')>Lauk</option>
                                    <option value="sambal" @selected(($option['tipe'] ?? '') === 'sambal')>Sambal</option>
                                    <option value="ekstra_lauk" @selected(($option['tipe'] ?? '') === 'ekstra_lauk')>Ekstra Lauk</option>
                                </select>
                            </td>
                            <td>
                                <select name="options[{{ $index }}][status]" class="form-select">
                                    <option value="tersedia" @selected(($option['status'] ?? 'tersedia') === 'tersedia')>Tersedia</option>
                                    <option value="habis" @selected(($option['status'] ?? '') === 'habis')>Habis</option>
                                </select>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-option-row" aria-label="Hapus opsi">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-options-row">
                            <td colspan="4" class="text-center text-muted py-3">Belum ada opsi tambahan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @error('options.*.nama_opsi')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Maksimal Pilih Lauk</label>
        <input type="number" name="maksimal_lauk" class="form-control" value="{{ old('maksimal_lauk', $menu->maksimal_lauk ?? 1) }}" min="1" max="10" required>
        @error('maksimal_lauk')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check">
            <input type="hidden" name="wajib_pilih_lauk" value="0">
            <input class="form-check-input" type="checkbox" name="wajib_pilih_lauk" value="1" id="wajib_pilih_lauk" @checked(old('wajib_pilih_lauk', $menu->wajib_pilih_lauk ?? false))>
            <label class="form-check-label" for="wajib_pilih_lauk">Wajib pilih lauk</label>
        </div>
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check">
            <input type="hidden" name="wajib_pilih_sambal" value="0">
            <input class="form-check-input" type="checkbox" name="wajib_pilih_sambal" value="1" id="wajib_pilih_sambal" @checked(old('wajib_pilih_sambal', $menu->wajib_pilih_sambal ?? false))>
            <label class="form-check-label" for="wajib_pilih_sambal">Wajib pilih sambal</label>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const optionRows = document.getElementById('optionRows');
        const addOptionRow = document.getElementById('addOptionRow');
        let optionIndex = optionRows.querySelectorAll('tr:not(.empty-options-row)').length;

        function removeEmptyRow() {
            optionRows.querySelector('.empty-options-row')?.remove();
        }

        function addRow(option = {}) {
            removeEmptyRow();
            const index = optionIndex++;
            const row = document.createElement('tr');
            row.setAttribute('data-option-row', 'true');
            row.innerHTML = `
                <td>
                    <input type="text" name="options[${index}][nama_opsi]" class="form-control" value="${option.nama_opsi ?? ''}" placeholder="Contoh: Ayam suwir">
                </td>
                <td>
                    <select name="options[${index}][tipe]" class="form-select">
                        <option value="lauk">Lauk</option>
                        <option value="sambal">Sambal</option>
                        <option value="ekstra_lauk">Ekstra Lauk</option>
                    </select>
                </td>
                <td>
                    <select name="options[${index}][status]" class="form-select">
                        <option value="tersedia">Tersedia</option>
                        <option value="habis">Habis</option>
                    </select>
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-option-row" aria-label="Hapus opsi">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            optionRows.appendChild(row);
        }

        optionRows.querySelectorAll('tr').forEach(row => {
            if (!row.classList.contains('empty-options-row')) {
                row.setAttribute('data-option-row', 'true');
            }
        });

        addOptionRow.addEventListener('click', () => addRow());
        optionRows.addEventListener('click', function (event) {
            const button = event.target.closest('.remove-option-row');
            if (!button) return;

            button.closest('tr').remove();
            if (!optionRows.querySelector('tr')) {
                optionRows.innerHTML = '<tr class="empty-options-row"><td colspan="4" class="text-center text-muted py-3">Belum ada opsi tambahan.</td></tr>';
            }
        });
    });
</script>
<hr class="my-4">
