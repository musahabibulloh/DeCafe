@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Ubah Menu</h2>
        <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.menus.update', $menu) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.menus.partials.form', ['menu' => $menu])
                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
            </form>
        </div>
    </div>
@endsection
