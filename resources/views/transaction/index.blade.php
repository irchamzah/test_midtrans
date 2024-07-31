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
                <th>Snap Token</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Detail Barang</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $transaction->snap_token }}</td>
                <td>{{ ucfirst($transaction->status) }}</td>
                <td>{{ $transaction->created_at->format('d-m-Y H:i:s') }}</td>
                <td>
                    <ul>
                        @foreach($transaction->items as $item)
                        <li>
                            @if($item->product)
                            {{ $item->product->name }} ({{ $item->quantity }})
                            @else
                            Produk tidak ditemukan ({{ $item->quantity }})
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </td>
                <td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                <td>
                    @if($transaction->status == 'Menunggu Pembayaran')
                    @if($transaction->snap_token)
                    <button type="button" class="btn btn-warning continue-payment-button"
                        data-snap-token="{{ $transaction->snap_token }}"
                        data-transaction-id="{{ $transaction->id }}">Lanjutkan Pembayaran</button>
                    <button type="button" class="btn btn-secondary cancel-order-button"
                        data-snap-token="{{ $transaction->snap_token }}"
                        data-transaction-id="{{ $transaction->id }}">Batalkan Pesanan</button>

                    @else
                    <button type="button" class="btn btn-success pay-button"
                        data-transaction-id="{{ $transaction->id }}">Bayar</button>
                    <button type="button" class="btn btn-danger delete-button"
                        data-transaction-id="{{ $transaction->id }}">Hapus</button>
                    @endif
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
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script>
    $(document).ready(function() {



        $('.pay-button').on('click', function() {
            var transactionId = $(this).data('transaction-id');
            
            $.ajax({
                url: '{{ route("transaction.pay") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    transaction_id: transactionId
                },
                success: function(response) {
                    snap.pay(response.snap_token, {
                        onSuccess: function(result) {
                            $.ajax({
                                url: '{{ route("transaction.updateStatus") }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    transaction_id: transactionId,
                                    status: 'Selesai'
                                },
                                success: function(response) {
                                    location.reload();
                                },
                                error: function(xhr) {
                                    alert('Failed to update transaction status: ' + xhr.responseJSON.message);
                                }
                            });
                        },
                        onPending: function(result) {
                            location.reload();
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

        $('.continue-payment-button').on('click', function() {
            var snapToken = $(this).data('snap-token');
            var transactionId = $(this).data('transaction-id');
            
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    $.ajax({
                        url: '{{ route("transaction.updateStatus") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            transaction_id: transactionId,
                            status: 'Selesai'
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('Failed to update transaction status: ' + xhr.responseJSON.message);
                        }
                    });
                },
                onPending: function(result) {
                    location.reload();
                },
                onError: function(result) {
                    alert('Payment failed: ' + result.status_message);
                }
            });
        });

        $('.cancel-order-button').on('click', function() {
            var transactionId = $(this).data('transaction-id');

            if (!confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) return;

            $.ajax({
                url: '{{ route("transaction.cancel") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    transaction_id: transactionId
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Failed to cancel transaction: ' + xhr.responseJSON.message);
                }
            });
        });

        $('.delete-button').on('click', function() {
            if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) return;

            var transactionId = $(this).data('transaction-id');
            
            $.ajax({
                url: '{{ route("transaction.delete") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    transaction_id: transactionId
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });
    });
</script>
@endpush