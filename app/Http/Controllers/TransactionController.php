<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

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
        Config::$serverKey = 'YOUR_MIDTRANS_SERVER_KEY';
        Config::$isProduction = false; // Set to true for production
        Config::$isSanitized = true;
        Config::$is3ds = true;

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
            'item_details' => $transaction->items->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'name' => $item->product->name,
                ];
            })->toArray(),
        ];

        // Get Snap Token from Midtrans
        $snapToken = Snap::getSnapToken($params);

        // Return Snap Token as JSON response
        return response()->json(['snap_token' => $snapToken]);
    }
}
