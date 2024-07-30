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
    <h2 class="mt-5">Keranjang</h2>
    <ul id="cart-items" class="list-group mb-4"></ul>
    <h3>Total: Rp <span id="cart-total">0</span></h3>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    function updateCartUI() {
        const cartItems = document.getElementById('cart-items');
        const cartTotal = document.getElementById('cart-total');
        cartItems.innerHTML = '';
        let total = 0;

        cart.forEach(item => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `${item.name} - ${item.quantity} x Rp ${item.price.toLocaleString('id-ID')} = Rp ${(item.quantity * item.price).toLocaleString('id-ID')}`
                + `<div>`
                + `<button class="btn btn-sm btn-success increase-quantity" data-id="${item.id}">+</button> `
                + `<button class="btn btn-sm btn-warning decrease-quantity" data-id="${item.id}">-</button> `
                + `<button class="btn btn-sm btn-danger remove-item" data-id="${item.id}">x</button>`
                + `</div>`;
            cartItems.appendChild(li);
            total += item.quantity * item.price;
        });

        cartTotal.textContent = total.toLocaleString('id-ID');
    }

    function saveCart() {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
    }

    document.querySelectorAll('.add-to-cart').forEach(function (button) {
        button.addEventListener('click', function () {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const productPrice = parseInt(this.getAttribute('data-price'));
            const productQuantity = parseInt(this.previousElementSibling.value);

            const existingProductIndex = cart.findIndex(item => item.id === productId);
            if (existingProductIndex > -1) {
                cart[existingProductIndex].quantity += productQuantity;
            } else {
                cart.push({ id: productId, name: productName, price: productPrice, quantity: productQuantity });
            }

            saveCart();
        });
    });

    document.getElementById('cart-items').addEventListener('click', function (event) {
        const target = event.target;
        if (target.classList.contains('increase-quantity')) {
            const productId = target.getAttribute('data-id');
            const productIndex = cart.findIndex(item => item.id === productId);
            if (productIndex > -1) {
                cart[productIndex].quantity += 1;
                saveCart();
            }
        } else if (target.classList.contains('decrease-quantity')) {
            const productId = target.getAttribute('data-id');
            const productIndex = cart.findIndex(item => item.id === productId);
            if (productIndex > -1) {
                cart[productIndex].quantity -= 1;
                if (cart[productIndex].quantity <= 0) {
                    cart.splice(productIndex, 1);
                }
                saveCart();
            }
        } else if (target.classList.contains('remove-item')) {
            const productId = target.getAttribute('data-id');
            const productIndex = cart.findIndex(item => item.id === productId);
            if (productIndex > -1) {
                cart.splice(productIndex, 1);
                saveCart();
            }
        }
    });

    updateCartUI();
});
</script>
@endsection