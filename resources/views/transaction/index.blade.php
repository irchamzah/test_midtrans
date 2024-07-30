@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Daftar Transaksi</h1>
    @if($transactions->isEmpty())
    <p>Belum ada transaksi.</p>
    @else
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Detail Barang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                <td>{{ ucfirst($transaction->status) }}</td>
                <td>{{ $transaction->created_at->format('d-m-Y H:i:s') }}</td>
                <td>
                    <ul>
                        @foreach($transaction->items as $item)
                        <li>{{ $item->product->name }} ({{ $item->quantity }})</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    @if($transaction->status == 'pending')
                    <button type="button" class="btn btn-success pay-button"
                        data-transaction-id="{{ $transaction->id }}">Bayar</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="YOUR_MIDTRANS_CLIENT_KEY"></script>
<script>
    $(document).ready(function() {
        $('.pay-button').on('click', function() {
            var transactionId = $(this).data('transaction-id');
            
            $.ajax({
                url: '{{ route("transaction.pay") }}', // Endpoint to get the Snap token
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    transaction_id: transactionId
                },
                success: function(response) {
                    snap.pay(response.snap_token, {
                        // Optional
                        onSuccess: function(result) {
                            location.reload(); // Reload page on success
                        },
                        onPending: function(result) {
                            location.reload(); // Reload page on pending
                        },
                        onError: function(result) {
                            alert('Payment failed: ' + result.status_message);
                        }
                    });
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });
    });
</script>
@endpush