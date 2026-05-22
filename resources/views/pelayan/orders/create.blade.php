@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Buat Pesanan</h2>
        <a href="{{ route('pelayan.orders.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('pelayan.orders.store') }}" method="POST">
                @csrf
                @include('pelayan.orders.partials.form', [
                    'order' => null,
                    'menus' => $menus,
                    'itemsMap' => collect(),
                ])
                <button class="btn btn-primary mt-3" type="submit">Simpan Pesanan</button>
            </form>
        </div>
    </div>
@endsection
