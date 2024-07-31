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
                <th>Order ID</th>
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
                <td>{{ $transaction->order_id }}</td>
                @if($transaction->status == 'Menunggu Pembayaran')
                <td>
                    <div class="alert alert-warning text-center" role="alert">{{ ucfirst($transaction->status) }}</div>
                </td>
                @elseif($transaction->status == 'Pending')
                <td>
                    <div class="alert alert-secondary text-center" role="alert">{{ ucfirst($transaction->status)
                        }}</div>
                </td>
                @elseif($transaction->status == 'Ditolak')
                <td>
                    <div class="alert alert-secondary text-center" role="alert">{{ ucfirst($transaction->status)
                        }}</div>
                </td>
                @elseif($transaction->status == 'Dibatalkan')
                <td>
                    <div class="alert alert-secondary text-center" role="alert">{{ ucfirst($transaction->status)
                        }}</div>
                </td>
                @elseif($transaction->status == 'Kedaluwarsa')
                <td>
                    <div class="alert alert-secondary text-center" role="alert">{{ ucfirst($transaction->status)
                        }}</div>
                </td>
                @elseif($transaction->status == 'Selesai')
                <td>
                    <div class="alert alert-success text-center" role="alert">{{ ucfirst($transaction->status)
                        }}</div>
                </td>
                @endif
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
                    @if($transaction->status == 'Pending')
                    <button type="button" class="btn btn-warning continue-payment-button"
                        data-snap-token="{{ $transaction->snap_token }}"
                        data-transaction-id="{{ $transaction->id }}">Lanjutkan Pembayaran</button>
                    @else
                    @if($transaction->status == 'Menunggu Pembayaran')
                    @if($transaction->snap_token)
                    <button type="button" class="btn btn-warning continue-payment-button"
                        data-snap-token="{{ $transaction->snap_token }}"
                        data-transaction-id="{{ $transaction->id }}">Lanjutkan Pembayaran</button>
                    <button type="button" class="btn btn-secondary cancel-order-button"
                        data-snap-token="{{ $transaction->snap_token }}" data-transaction-id="{{ $transaction->id }}"
                        data-order-id="{{ $transaction->order_id }}">Batalkan Pesanan</button>
                    <button type="button" class="btn btn-info sync-status-button"
                        data-order-id="{{ $transaction->order_id }}">Sync Status</button>
                    @else
                    <button type="button" class="btn btn-success pay-button"
                        data-transaction-id="{{ $transaction->id }}"
                        data-order-id="{{ $transaction->order_id }}">Bayar</button>
                    <button type="button" class="btn btn-danger delete-button"
                        data-transaction-id="{{ $transaction->id }}">Hapus</button>
                    @endif
                    @endif
                    @endif

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script>
    $(document).ready(function() {
        // Function untuk Button Bayar
        $('.pay-button').on('click', function() {
            var transactionId = $(this).data('transaction-id');
            var orderId = $(this).data('order-id');
            
            $.ajax({
                url: '{{ route("transaction.pay") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    transaction_id: transactionId,
                    order_id: orderId
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });

        // Function untuk Button Lanjutkan Pembayaran
        $('.continue-payment-button').on('click', function() {
            var snapToken = $(this).data('snap-token');
            var transactionId = $(this).data('transaction-id');
            var orderId = $(this).data('order-id');
            
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    $.ajax({
                        url: '{{ route("transaction.updateStatus") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            transaction_id: transactionId,
                            order_id: orderId,
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
                    $.ajax({
                                url: '{{ route("transaction.updateStatus") }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    transaction_id: transactionId,
                                    status: 'Menunggu Pembayaran'
                                },
                                success: function(response) {
                                    location.reload();
                                },
                                error: function(xhr) {
                                    alert('Failed to update transaction status: ' + xhr.responseJSON.message);
                                }
                            });
                },
                onError: function(result) {
                    alert('Payment failed: ' + result.status_message);
                    // Jika gagal, coba jalankan sync status
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
                }
            });
        });

        // Function untuk Button Batalkan Pesanan
        $('.cancel-order-button').on('click', function() {
            var orderId = $(this).data('order-id');
            var transactionId = $(this).data('transaction-id');

            if (!confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) return;

            $.ajax({
                url: '{{ route("transaction.cancel") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: orderId,
                    transaction_id: transactionId
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Failed to cancel transaction: ' + xhr.responseJSON.message);
                    // Jika gagal, coba jalankan sync status
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
                    
                }
            });
        });

        // Function untuk Button Hapus
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

        // Function untuk Button Sync Status
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
                location.reload();
            },
            error: function(xhr) {
                alert('Failed to sync transaction status: ' + xhr.responseJSON.message);
            }
        });
    });
    });
</script>
@endpush