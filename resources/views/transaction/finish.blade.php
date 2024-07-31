@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pesananmu telah dibatalkan oleh penjual</h1>
    <p>Your order ID: {{ $orderId }}</p>
    <button class="btn btn-primary sync-status-button" data-order-id="{{ $orderId }}">Kembali ke halaman
        transaksi</button>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Function untuk Tombol Sync Status
        $('.sync-status-button').on('click', function() {
        var orderId = $(this).data('order-id');
        
        $.ajax({
            url: '{{ route("transaction.syncStatus") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                order_id: orderId
            },
            success: function(response) {
                window.location.href = '{{ route("transaction.index") }}';
            },
            error: function(xhr) {
                alert('Failed to sync transaction status: ' + xhr.responseJSON.message);
            }
        });
    });
    });
</script>
@endpush