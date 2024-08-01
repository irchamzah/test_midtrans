<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

class TransactionController extends Controller
{
    public function __construct()
    {
        // Cek apakah user sudah login
        $this->middleware('auth');
    }
    public function index()
    {
        $transactions = Transaction::where('user_id', auth()->id())->with('items.product')->orderBy('created_at', 'desc')->paginate(5);

        // AKTIFKAN JIKA INGIN SELALU MEMPERBARUI STATUS TRANSAKSI SETIAP DI REFRESH
        // foreach ($transactions as $transaction) {
        //     app('App\Http\Controllers\TransactionController')->syncStatus($transaction->order_id);
        // }

        return view('transaction.index', compact('transactions'));
    }

    public function pay(Request $request)
    {
        // Validasi data
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);

        // Midtrans Configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);

        // Membuat item details
        $itemDetails = $transaction->items->map(function ($item) {
            if ($item->product) {
                return [
                    'id' => $item->product_id,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'name' => $item->product->name,
                ];
            } else {
                return [
                    'id' => $item->product_id,
                    'price' => 0,
                    'quantity' => $item->quantity,
                    'name' => 'Product not found',
                ];
            }
        })->toArray();


        // Membuat Midtrans Snap parameters
        $params = [
            'transaction_details' => [
                'order_id' => $request->order_id,
                'gross_amount' => $transaction->total_price,
            ],
            'customer_details' => [
                'first_name' => $transaction->user->name,
                'email' => $transaction->user->email,
            ],
            'item_details' => $itemDetails,
            'callbacks' => [
                'finish' => route('transaction.finish'),
                'unfinish' => route('transaction.unfinish'),
                'error' => route('transaction.error'),
            ],
        ];

        // Ambil Snap Token dari Midtrans
        $snapToken = Snap::getSnapToken($params);

        $transaction->order_id = $request->order_id;
        $transaction->snap_token = $snapToken;
        $transaction->status = 'Pending';
        $transaction->save();

        // Kirim Snap Token sebagai JSON response
        return response()->json(['snap_token' => $snapToken]);
    }

    public function delete(Request $request)
    {
        // Validasi data
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);

        // Hanya bisa menghapus transaksi yang statusnya 'Menunggu Pembayaran'
        if ($transaction->status == 'Menunggu Pembayaran') {
            $transaction->delete();
            return response()->json(['message' => 'Transaksi berhasil dihapus.']);
        }

        return response()->json(['message' => 'Transaksi tidak dapat dihapus.'], 400);
    }

    public function updateStatus(Request $request)
    {
        // Validasi data
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'status' => 'required|in:Menunggu Pembayaran,Selesai,Dibatalkan',
        ]);

        // Cari dan ubah status transaksi
        $transaction = Transaction::findOrFail($request->transaction_id);
        $transaction->status = $request->status;
        $transaction->save();

        return response()->json(['message' => 'Transaction status updated successfully']);
    }

    public function cancel(Request $request)
    {
        // Validasi data
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        $transaction = Transaction::where('order_id', $request->order_id)->firstOrFail();

        // Midtrans Configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);

        try {
            $cancel = \Midtrans\Transaction::cancel($request->order_id);

            // Cari dan ubah status transaksi
            $transaction->status = 'Dibatalkan';
            $transaction->save();

            return response()->json(['message' => 'Transaction cancelled successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to cancel transaction'], 500);
        }
    }
    public function getUnpaidCount()
    {
        // Menghitung jumlah transaksi yang belum terbayar
        $unpaidCount = Transaction::where('user_id', auth()->id())
            ->whereNotIn('status', ['Selesai', 'Dibatalkan', 'Ditolak', 'Kedaluwarsa'])
            ->count();

        return response()->json($unpaidCount);
    }

    public function syncStatus(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);

        try {
            /** @var \stdClass $status */
            // Ambil status dari Midtrans
            $status = MidtransTransaction::status($request->order_id);
            $transaction = Transaction::where('order_id', $request->order_id)->firstOrFail();

            // Menyimpan status dari Midtrans ke database
            $transaction->status = $this->mapMidtransStatus($status->transaction_status);
            $transaction->save();

            return response()->json(['message' => 'Transaction status synced successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to sync transaction status: ' . $e->getMessage()], 500);
        }
    }

    private function mapMidtransStatus($midtransStatus)
    {
        // Mengubah status Midtrans ke status yang sesuai
        switch ($midtransStatus) {
            case 'settlement':
                return 'Selesai';
            case 'pending':
                return 'Menunggu Pembayaran';
            case 'deny':
                return 'Ditolak';
            case 'cancel':
                return 'Dibatalkan';
            case 'expire':
                return 'Kedaluwarsa';
            default:
                return 'Kedaluwarsa';
        }
    }

    public function finish(Request $request)
    {
        // Jika payment sukses
        $orderId = $request->input('order_id');
        return view('transaction.finish', compact('orderId'));
    }

    public function unfinish(Request $request)
    {
        // Jika payment unfinish
        $orderId = $request->input('order_id');
        return view('transaction.unfinish', compact('orderId'));
    }

    public function error(Request $request)
    {
        // Jika payment error
        $orderId = $request->input('order_id');
        return view('transaction.error', compact('orderId'));
    }
}
