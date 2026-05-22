@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Ubah Pesanan</h2>
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
            <form action="{{ route('pelayan.orders.update', $order) }}" method="POST">
                @csrf
                @method('PUT')
                @include('pelayan.orders.partials.form', [
                    'order' => $order,
                    'menus' => $menus,
                    'itemsMap' => $itemsMap,
                ])
                <button class="btn btn-primary mt-3" type="submit">Simpan Perubahan</button>
            </form>
        </div>
    </div>
@endsection
