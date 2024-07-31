@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Keranjang Belanja</h1>
    <div class="row">
        @if($cartItems->isEmpty())
        <div class="col-md-12">
            <div class="alert alert-info text-center">
                Keranjang Anda kosong. Silakan <a href="/">tambahkan produk</a> ke keranjang.
            </div>
        </div>
        @else
        @foreach($cartItems as $item)
        <div class="col-md-4 mb-4">
            <div class="card">
                @if($item->product->image)
                <img class="card-img-top" src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $item->product->name }}</h5>
                    <p class="card-text">{{ $item->product->description }}</p>
                    <p class="card-text"><strong>Rp {{ number_format($item->product->price, 0, ',', '.') }}</strong></p>
                    <div class="d-flex align-items-center">
                        <input type="number" class="form-control mr-2 quantity-input" value="{{ $item->quantity }}"
                            data-id="{{ $item->id }}" min="1">
                        <button type="button" class="btn btn-warning update-quantity"
                            data-id="{{ $item->id }}">Update</button>
                        <button type="button" class="btn btn-danger ml-2 remove-from-cart"
                            data-id="{{ $item->id }}">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif

    </div>
    @if($cartItems->isEmpty())
    @else
    <div class="mt-4">
        <h4>Total Harga: <strong>Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong></h4>
        <a href="{{ route('cart.checkout') }}" class="btn btn-primary">Checkout</a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Hapus produk dari keranjang
        $('.remove-from-cart').on('click', function() {
            var id = $(this).data('id');
            $.ajax({
                url: '{{ route('cart.remove') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: id
                },
                success: function(response) {
                    alert(response.message);
                    location.reload();
                }
            });
        });

        // Update jumlah produk di keranjang
        $('.update-quantity').on('click', function() {
            var id = $(this).data('id');
            var quantity = $(this).siblings('.quantity-input').val();
            $.ajax({
                url: '{{ route('cart.update') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: id,
                    quantity: quantity
                },
                success: function(response) {
                    alert(response.message);
                    location.reload();
                }
            });
        });
    });
</script>
@endpush