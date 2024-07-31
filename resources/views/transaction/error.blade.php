@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Payment Error</h1>
    <p>Your order ID: {{ $orderId }}</p>
    <a href="{{ route('transaction.index') }}" class="btn btn-primary">Return to Home</a>
</div>
@endsection