@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Product List</h1>
    <div class="row">
        @foreach($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card">
                @if($product->image)
                <img class="card-img-top" src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text">{{ $product->description }}</p>
                    <p class="card-text"><strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong></p>
                    <input type="number" placeholder="Jumlah Produk" min="1" value="1">
                    <button type="button" class="btn btn-primary">Tambah ke keranjang</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection