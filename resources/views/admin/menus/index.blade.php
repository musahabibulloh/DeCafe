@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Menu</h2>
        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">Tambah Menu</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Desktop View -->
            <div class="d-none d-md-block table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menus as $menu)
                            <tr>
                                <td>{{ $menu->nama_menu }}</td>
                                <td>{{ ucfirst($menu->kategori) }}</td>
                                <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td>{{ $menu->stok }}</td>
                                <td>
                                    <span class="badge bg-{{ $menu->status === 'tersedia' ? 'success' : ($menu->status === 'habis' ? 'warning' : 'secondary') }}">
                                        {{ str_replace('_', ' ', $menu->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.menus.show', $menu) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                    <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                                    <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus menu ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada menu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="d-md-none">
                @forelse ($menus as $menu)
                    <div class="card border-0 shadow-sm mb-3" style="background-color: var(--bs-card-bg); border: 1px solid rgba(var(--bs-body-color-rgb), 0.1) !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-primary">{{ $menu->nama_menu }}</h6>
                                <span class="badge bg-{{ $menu->status === 'tersedia' ? 'success' : ($menu->status === 'habis' ? 'warning' : 'secondary') }}">
                                    {{ str_replace('_', ' ', $menu->status) }}
                                </span>
                            </div>
                            <hr class="my-2 opacity-50" style="color: var(--bs-body-color);">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Kategori</small>
                                    <span class="fw-semibold">{{ ucfirst($menu->kategori) }}</span>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block">Harga</small>
                                    <span class="fw-bold text-success">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Stok</small>
                                <span class="fw-semibold">{{ $menu->stok }}</span>
                            </div>
                            <div class="d-grid gap-2 d-flex justify-content-end mt-2 pt-2 border-top" style="border-top-color: rgba(var(--bs-body-color-rgb), 0.1) !important;">
                                <a href="{{ route('admin.menus.show', $menu) }}" class="btn btn-sm btn-outline-secondary px-3">Detail</a>
                                <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-sm btn-outline-primary px-3">Ubah</a>
                                <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Hapus menu ini?')">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Belum ada menu.</div>
                @endforelse
            </div>

            {{ $menus->links() }}
        </div>
    </div>
@endsection
