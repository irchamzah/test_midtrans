<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', auth()->id())->with('items.product')->get();
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
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $itemDetails = $transaction->items->map(function ($item) {
            // Periksa apakah produk ada
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
                'order_id' => $transaction->id,
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
        $transaction->snap_token = $snapToken;
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

        $transaction = Transaction::findOrFail($request->transaction_id);

        // Midtrans Configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false; // Set to true for production
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $cancel = \Midtrans\Transaction::cancel($transaction->id);

            // Update transaction status in database
            $transaction->status = 'Dibatalkan';
            $transaction->save();

            return response()->json(['message' => 'Transaction cancelled successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to cancel transaction'], 500);
        }
    }
}
