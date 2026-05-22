@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Ubah User</h2>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.users.partials.form', ['user' => $user])
                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
            </form>
        </div>
    </div>
@endsection
