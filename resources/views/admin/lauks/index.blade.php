@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Lauk & Ekstra</h2>
        <a href="{{ route('admin.lauks.create') }}" class="btn btn-primary">Tambah Lauk / Ekstra</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Desktop View -->
            <div class="d-none d-md-block table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Lauk / Ekstra</th>
                            <th>Tipe</th>
                            <th>Harga Reguler / Tambahan</th>
                            <th>Lauk Premium?</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lauks as $lauk)
                            <tr>
                                <td>
                                    @if ($lauk->gambar_url)
                                        <img src="{{ $lauk->gambar_url }}" alt="{{ $lauk->nama_lauk }}" class="img-thumbnail" style="height: 40px; width: 40px; object-fit: cover;">
                                    @else
                                        <span class="text-muted small">No Image</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $lauk->nama_lauk }}</td>
                                <td>
                                    <span class="badge bg-{{ $lauk->tipe === 'utama' ? 'primary' : 'info' }}">
                                        {{ $lauk->tipe === 'utama' ? 'Lauk Utama' : 'Ekstra Lauk' }}
                                    </span>
                                </td>
                                <td>Rp {{ number_format($lauk->harga, 0, ',', '.') }}</td>
                                <td>
                                    @if($lauk->is_premium)
                                        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> Premium</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.lauks.edit', $lauk) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                                    <form action="{{ route('admin.lauks.destroy', $lauk) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus lauk ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data lauk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="d-md-none">
                @forelse ($lauks as $lauk)
                    <div class="card border-0 shadow-sm mb-3" style="background-color: var(--bs-card-bg); border: 1px solid rgba(var(--bs-body-color-rgb), 0.1) !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                @if ($lauk->gambar_url)
                                    <img src="{{ $lauk->gambar_url }}" alt="{{ $lauk->nama_lauk }}" class="img-thumbnail" style="height: 50px; width: 50px; object-fit: cover;">
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0 text-primary">{{ $lauk->nama_lauk }}</h6>
                                    <span class="badge bg-{{ $lauk->tipe === 'utama' ? 'primary' : 'info' }} mt-1">
                                        {{ $lauk->tipe === 'utama' ? 'Lauk Utama' : 'Ekstra Lauk' }}
                                    </span>
                                </div>
                            </div>
                            <hr class="my-2 opacity-50" style="color: var(--bs-body-color);">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Harga</small>
                                    <span class="fw-bold text-success">Rp {{ number_format($lauk->harga, 0, ',', '.') }}</span>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block">Premium?</small>
                                    @if($lauk->is_premium)
                                        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Premium</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-flex justify-content-end mt-2 pt-2 border-top" style="border-top-color: rgba(var(--bs-body-color-rgb), 0.1) !important;">
                                <a href="{{ route('admin.lauks.edit', $lauk) }}" class="btn btn-sm btn-outline-primary px-3">Ubah</a>
                                <form action="{{ route('admin.lauks.destroy', $lauk) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Hapus lauk ini?')">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Belum ada data lauk.</div>
                @endforelse
            </div>

            {{ $lauks->links() }}
        </div>
    </div>
@endsection
