<?php

use App\Http\Controllers\Seller;
use App\Http\Controllers\ShopController;
use App\Http\Middleware\SellerAuth;
use Illuminate\Support\Facades\Route;

// ===== Pembeli =====
Route::get('/', [ShopController::class, 'index'])->name('shop');
Route::post('/pesan', [ShopController::class, 'checkout'])->name('checkout')->middleware('throttle:20,1');
Route::get('/pesanan/{code}', [ShopController::class, 'thanks'])->name('thanks');

// ===== Penjual =====
Route::prefix('penjual')->group(function () {
    Route::get('login', [Seller\AuthController::class, 'showLogin'])->name('seller.login');
    Route::post('login', [Seller\AuthController::class, 'login'])->name('seller.login.post')->middleware('throttle:5,1');

    Route::middleware(SellerAuth::class)->name('seller.')->group(function () {
        Route::post('logout', [Seller\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [Seller\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', Seller\ProductController::class)->except('show');

        Route::get('kasbon', [Seller\KasbonController::class, 'index'])->name('kasbon.index');
        Route::get('kasbon/create', [Seller\KasbonController::class, 'create'])->name('kasbon.create');
        Route::post('kasbon', [Seller\KasbonController::class, 'store'])->name('kasbon.store');
        Route::get('kasbon/{debtor}', [Seller\KasbonController::class, 'show'])->name('kasbon.show');
        Route::put('kasbon/{debtor}', [Seller\KasbonController::class, 'update'])->name('kasbon.update');
        Route::delete('kasbon/{debtor}', [Seller\KasbonController::class, 'destroy'])->name('kasbon.destroy');
        Route::post('kasbon/{debtor}/hutang', [Seller\KasbonController::class, 'addDebt'])->name('kasbon.debt');
        Route::post('kasbon/{debtor}/bayar', [Seller\KasbonController::class, 'pay'])->name('kasbon.pay');
        Route::delete('kasbon/{debtor}/entries/{entry}', [Seller\KasbonController::class, 'destroyEntry'])->name('kasbon.entries.destroy');

        Route::get('kasir', [Seller\KasirController::class, 'index'])->name('kasir.index');
        Route::post('kasir', [Seller\KasirController::class, 'store'])->name('kasir.store');

        Route::get('stok', [Seller\StockController::class, 'index'])->name('stock.index');
        Route::post('stok', [Seller\StockController::class, 'update'])->name('stock.update');

        Route::get('orders', [Seller\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Seller\OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/confirm', [Seller\OrderController::class, 'confirm'])->name('orders.confirm');
        Route::post('orders/{order}/cancel', [Seller\OrderController::class, 'cancel'])->name('orders.cancel');

        Route::get('transactions', [Seller\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/create', [Seller\TransactionController::class, 'create'])->name('transactions.create');
        Route::post('transactions', [Seller\TransactionController::class, 'store'])->name('transactions.store');
        Route::delete('transactions/{transaction}', [Seller\TransactionController::class, 'destroy'])->name('transactions.destroy');
    });
});
