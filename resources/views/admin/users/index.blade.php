@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data User</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Tambah User</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Desktop View -->
            <div class="d-none d-md-block table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-dark">{{ ucfirst($user->role) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus user ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="d-md-none">
                @forelse ($users as $user)
                    <div class="card border-0 shadow-sm mb-3" style="background-color: var(--bs-card-bg); border: 1px solid rgba(var(--bs-body-color-rgb), 0.1) !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-primary">{{ $user->name }}</h6>
                                <span class="badge bg-dark">{{ ucfirst($user->role) }}</span>
                            </div>
                            <hr class="my-2 opacity-50" style="color: var(--bs-body-color);">
                            <div class="mb-3">
                                <small class="text-muted d-block">Email</small>
                                <span class="fw-semibold">{{ $user->email }}</span>
                            </div>
                            <div class="d-grid gap-2 d-flex justify-content-end mt-2 pt-2 border-top" style="border-top-color: rgba(var(--bs-body-color-rgb), 0.1) !important;">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary px-3">Detail</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary px-3">Ubah</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Hapus user ini?')">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Belum ada user.</div>
                @endforelse
            </div>
            {{ $users->links() }}
        </div>
    </div>
@endsection
