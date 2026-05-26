<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderLookupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::get('/productos/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::post('/carro/agregar', [CartController::class, 'add'])->name('cart.add');
Route::get('/carro', [CartController::class, 'index'])->name('cart.index');
Route::patch('/carro/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carro/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/procesar', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/exito', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/error', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

Route::get('/buscar-orden', [OrderLookupController::class, 'showForm'])->name('orders.lookup');
Route::post('/buscar-orden', [OrderLookupController::class, 'search'])->name('orders.search');

Route::middleware('auth')->group(function () {
    Route::get('/mis-ordenes', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/mis-ordenes/{order}', [OrderController::class, 'show'])->name('orders.show');
});

Route::post('/pago/confirmar', [PaymentController::class, 'confirm'])->name('payment.confirm');
Route::match(['GET', 'POST'], '/pago/retorno', [PaymentController::class, 'return'])->name('payment.return');

// Temporal routes removed after successful migration

Route::get('/imgProduct/{filename}', function ($filename) {
    $path = base_path('imgProduct/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->where('filename', '.*');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', AdminProductController::class);
    Route::delete('/productos/{product}/imagenes/{image}', [AdminProductController::class, 'deleteImage'])
        ->name('products.images.destroy');
    Route::get('/ordenes', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/ordenes/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/ordenes/{order}/estado', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
});
