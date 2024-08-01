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

                {{-- Status transaksi --}}
                @php
                $statusClasses = [
                'Menunggu Pembayaran' => 'warning',
                'Pending' => 'secondary',
                'Ditolak' => 'secondary',
                'Dibatalkan' => 'secondary',
                'Kedaluwarsa' => 'secondary',
                'Selesai' => 'success',
                ];
                @endphp
                <td>
                    <div class="alert alert-{{ $statusClasses[$transaction->status] ?? 'secondary' }} text-center"
                        role="alert">
                        {{ ucfirst($transaction->status) }}
                    </div>
                </td>
                {{-- Status transaksi end --}}
                <td>{{ $transaction->created_at->format('d-m-Y H:i:s') }}</td>
                <td>
                    <ul>
                        @foreach($transaction->items as $item)
                        <li>
                            {{ $item->product->name }} ({{ $item->quantity }})
                        </li>
                        @endforeach
                    </ul>
                </td>
                <td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                <td>
                    {{-- Tombol Aksi --}}
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
                    {{-- Tombol Aksi end --}}
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

{{-- Jika mode Development maka pakai sandbox, jika tidak maka pakai Production --}}
<script
    src="{{ env('MIDTRANS_IS_PRODUCTION', false) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

<script>
    $(document).ready(function() {
        // Function untuk Tombol Bayar
        $('.pay-button').on('click', function() {
            var transactionId = $(this).data('transaction-id');
            var orderId = $(this).data('order-id');
            // Menjalankan transaction.pay untuk mengenerate snapToken, snapToken akan digunakan di tombol "Lanjutkan Pembayaran"
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

        // Function untuk Tombol "Lanjutkan Pembayaran"
        $('.continue-payment-button').on('click', function() {
            var snapToken = $(this).data('snap-token');
            var transactionId = $(this).data('transaction-id');
            var orderId = $(this).data('order-id');
            // Menjalankan snap midtrans
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    // Jika pembayaran transaksi sukses, ubah status menjadi Selesai
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
                    // Jika transaksi pending, ubah status menjadi Menunggu Pembayaran
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
                    alert('Failed to continue payment: ' + result.error_messages);
                }
            });
        });

        // Function untuk Tombol Batalkan Pesanan
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
                // jika berhasil akan reload.
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Failed to cancel transaction: ' + xhr.responseJSON.message);
                    // Jika gagal, coba jalankan sync status
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
                    
                }
            });
        });

        // Function untuk Tombol Hapus
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