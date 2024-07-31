<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', auth()->id())->with('items.product')->orderBy('created_at', 'desc')->paginate(5);
        return view('transaction.index', compact('transactions'));
    }

    public function pay(Request $request)
    {
        // Validate the request
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        // Find the transaction
        $transaction = Transaction::findOrFail($request->transaction_id);

        // Midtrans Configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = true;
        Config::$isSanitized = true;
        Config::$is3ds = true;

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


        // Create Midtrans Snap parameters
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
        ];

        // Get Snap Token from Midtrans
        $snapToken = Snap::getSnapToken($params);

        $transaction->order_id = $request->order_id;
        $transaction->snap_token = $snapToken;
        $transaction->status = 'Pending';
        $transaction->save();

        // Return Snap Token as JSON response
        return response()->json(['snap_token' => $snapToken]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);

        if ($transaction->status == 'Menunggu Pembayaran') {
            $transaction->delete();
            return response()->json(['message' => 'Transaksi berhasil dihapus.']);
        }

        return response()->json(['message' => 'Transaksi tidak dapat dihapus.'], 400);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'status' => 'required|in:Menunggu Pembayaran,Selesai,Dibatalkan',
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);
        $transaction->status = $request->status;
        $transaction->save();

        return response()->json(['message' => 'Transaction status updated successfully']);
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
        ]);


        $transaction = Transaction::where('order_id', $request->order_id)->firstOrFail();
        // dd($transaction);

        // Midtrans Configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = true; // Set to true for production
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $cancel = \Midtrans\Transaction::cancel($request->order_id);

            // Update transaction status in database
            $transaction->status = 'Dibatalkan';
            $transaction->save();

            return response()->json(['message' => 'Transaction cancelled successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to cancel transaction'], 500);
        }
    }
    public function getUnpaidCount()
    {
        // Menghitung jumlah transaksi yang belum terbayar (statusnya bukan 'Selesai')
        $unpaidCount = Transaction::where('user_id', auth()->id())
            ->whereNotIn('status', ['Selesai', 'Dibatalkan', 'Ditolak', 'Kedaluwarsa'])
            ->count();

        return response()->json($unpaidCount);
    }

    public function syncStatus(Request $request)
    {
        // dd($request->order_id);
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = true; // Set to true for production
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            /** @var \stdClass $status */
            // Ambil status dari Midtrans
            $status = MidtransTransaction::status($request->order_id);
            $transaction = Transaction::where('order_id', $request->order_id)->firstOrFail();


            // Sinkronkan status di database
            $transaction->status = $this->mapMidtransStatus($status->transaction_status);
            $transaction->save();
            // dd($transaction);

            return response()->json(['message' => 'Transaction status synced successfully.']);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Failed to sync transaction status: ' . $e->getMessage()], 500);
        }
    }

    // Metode untuk memetakan status dari Midtrans ke status lokal
    private function mapMidtransStatus($midtransStatus)
    {
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
}
