@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Menu</h2>
        <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama Menu:</strong> {{ $menu->nama_menu }}</p>
                    <p><strong>Kategori:</strong> {{ ucfirst($menu->kategori) }}</p>
                    <p><strong>Harga:</strong> Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                    <p><strong>Stok:</strong> {{ $menu->stok }}</p>
                    <p><strong>Status:</strong> {{ str_replace('_', ' ', $menu->status) }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Deskripsi:</strong></p>
                    <p>{{ $menu->deskripsi ?? '-' }}</p>
                    @if ($menu->gambar_url)
                        <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama_menu }}" class="img-fluid rounded">
                    @else
                        <p class="text-muted">Tidak ada gambar.</p>
                    @endif
                </div>
            </div>

            <hr class="my-4">

            @php
                $optionsByType = $menu->options->groupBy('tipe');
            @endphp

            <h5 class="mb-3">Opsi Tambahan</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <p class="fw-semibold mb-2">Jenis Lauk</p>
                    @if($optionsByType->get('lauk', collect())->isNotEmpty())
                        <ul class="list-group list-group-flush">
                            @foreach($optionsByType->get('lauk') as $opsi)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $opsi->nama_opsi }}</span>
                                    <span class="badge bg-{{ $opsi->status === 'tersedia' ? 'success' : 'danger' }}">{{ ucfirst($opsi->status) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <small class="text-muted d-block mt-2">
                            Maksimal {{ $menu->maksimal_lauk }} pilihan.
                            {{ $menu->wajib_pilih_lauk ? 'Wajib dipilih.' : 'Opsional.' }}
                        </small>
                    @else
                        <p class="text-muted">Tidak ada opsi lauk.</p>
                    @endif
                </div>
                <div class="col-md-4">
                    <p class="fw-semibold mb-2">Jenis Sambal</p>
                    @if($optionsByType->get('sambal', collect())->isNotEmpty())
                        <ul class="list-group list-group-flush">
                            @foreach($optionsByType->get('sambal') as $opsi)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $opsi->nama_opsi }}</span>
                                    <span class="badge bg-{{ $opsi->status === 'tersedia' ? 'success' : 'danger' }}">{{ ucfirst($opsi->status) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <small class="text-muted d-block mt-2">
                            {{ $menu->wajib_pilih_sambal ? 'Wajib dipilih.' : 'Opsional.' }}
                        </small>
                    @else
                        <p class="text-muted">Tidak ada opsi sambal.</p>
                    @endif
                </div>
                <div class="col-md-4">
                    <p class="fw-semibold mb-2">Ekstra Lauk</p>
                    @if($optionsByType->get('ekstra_lauk', collect())->isNotEmpty())
                        <ul class="list-group list-group-flush">
                            @foreach($optionsByType->get('ekstra_lauk') as $opsi)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $opsi->nama_opsi }}</span>
                                    <span class="badge bg-{{ $opsi->status === 'tersedia' ? 'success' : 'danger' }}">{{ ucfirst($opsi->status) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Tidak ada ekstra lauk.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
