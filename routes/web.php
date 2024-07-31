<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', [App\Http\Controllers\ProductController::class, 'index']);
Route::get('/home', [App\Http\Controllers\ProductController::class, 'index']);

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction.index');
Route::post('/transaction/pay', [TransactionController::class, 'pay'])->name('transaction.pay');
Route::post('/transaction/delete', [TransactionController::class, 'delete'])->name('transaction.delete');
Route::post('/transaction/update-status', [TransactionController::class, 'updateStatus'])->name('transaction.updateStatus');
Route::post('/transaction/cancel', [TransactionController::class, 'cancel'])->name('transaction.cancel');
Route::get('/transaction/unpaid-count', [TransactionController::class, 'getUnpaidCount'])->name('transaction.unpaidCount');
Route::post('/transaction/sync-status', [TransactionController::class, 'syncStatus'])->name('transaction.syncStatus');



Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
