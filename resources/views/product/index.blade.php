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
                    <input type="number" class="product-quantity" placeholder="Jumlah Produk" min="1" value="1">
                    <button type="button" class="btn btn-primary add-to-cart" data-id="{{ $product->id }}"
                        data-name="{{ $product->name }}" data-price="{{ $product->price }}">Tambah ke keranjang</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Tambah produk ke keranjang
    $('.add-to-cart').on('click', function() {
        var id = $(this).data('id');
        var quantity = $(this).siblings('.product-quantity').val();
        $.ajax({
            url: '{{ route('cart.add') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: id,
                quantity: quantity
            },
            success: function(response) {
                alert(response.message);
            },
            error: function(response) {
                if (response.status === 401) {
                        window.location.href = '{{ route('login') }}';
                    } else {
                        alert('Error: ' + response.responseJSON.message);
                    }
            }
        });
    });
});
</script>
@endpush