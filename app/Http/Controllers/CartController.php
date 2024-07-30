<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();
        $totalPrice = $cartItems->reduce(function ($carry, $item) {
            return $carry + ($item->product->price * $item->quantity);
        }, 0);
        return view('cart.index', compact('cartItems', 'totalPrice'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $request->input('product_id')],
            ['quantity' => DB::raw('quantity + ' . $request->input('quantity'))]
        );

        return response()->json(['success' => true, 'message' => 'Product added to cart']);
    }

    public function update(Request $request)
    {
        $cartItem = Cart::where('user_id', auth()->id())
            ->where('id', $request->input('product_id'))
            ->first();

        if ($cartItem) {
            $cartItem->quantity = $request->input('quantity');
            $cartItem->save();
            return response()->json(['success' => true, 'message' => 'Quantity updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Item not found']);
    }


    public function remove(Request $request)
    {

        $userId = auth()->id();
        $productId = $request->input('product_id');

        error_log("User ID: $userId");
        error_log("Product ID: $productId");

        $cart = Cart::where('user_id', auth()->id())->where('id', $request->input('product_id'))->delete();
        return response()->json(['success' => true, 'message' => 'Product removed from cart']);
    }

    public function checkout()
    {
        $userId = auth()->id();
        $cartItems = Cart::where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong!');
        }

        // Buat transaksi baru
        $transaction = Transaction::create([
            'user_id' => $userId,
            'total_price' => $cartItems->reduce(function ($carry, $item) {
                return $carry + ($item->product->price * $item->quantity);
            }, 0),
            'status' => 'pending',
        ]);

        // Simpan item dari keranjang ke dalam transaction_items
        foreach ($cartItems as $cartItem) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
            ]);
        }

        // Hapus item dari keranjang setelah transaksi dibuat
        Cart::where('user_id', $userId)->delete();

        // Redirect ke halaman transaksi
        return redirect()->route('transaction.index');
    }
}
