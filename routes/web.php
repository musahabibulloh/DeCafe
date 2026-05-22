<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Kasir\CashierOrderController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\PaymentController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Pelayan\DashboardController as PelayanDashboardController;
use App\Http\Controllers\Pelayan\OrderController as PelayanOrderController;
use App\Http\Controllers\Dapur\DashboardController as DapurDashboardController;
use App\Http\Controllers\Dapur\KitchenOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process')->middleware('throttle:login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('/menus', MenuController::class);
    Route::resource('/users', UserController::class);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Kasir routes
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [CashierOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [CashierOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
});

// Customer routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'index'])->name('dashboard');
    Route::get('/menus', [CustomerController::class, 'menus'])->name('menus');
    Route::get('/orders/create', [CustomerController::class, 'createOrder'])->name('orders.create');
    Route::post('/orders', [CustomerController::class, 'storeOrder'])->name('orders.store');
    Route::get('/orders', [CustomerController::class, 'myOrders'])->name('orders.index');
    Route::get('/orders/{order}', [CustomerController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{order}/pay', [CustomerController::class, 'payOrder'])->name('orders.pay');
    Route::get('/orders/{order}/qris', [CustomerController::class, 'showQrisPayment'])->name('orders.qris');
    Route::post('/orders/{order}/qris/upload', [CustomerController::class, 'uploadQrisPayment'])->name('orders.qris.upload');
    Route::get('/orders/{order}/status', [CustomerController::class, 'checkOrderStatus'])->name('orders.status');
    Route::post('/orders/{order}/simulate-qris-pay', [CustomerController::class, 'simulateQrisPay'])->name('orders.simulate-qris-pay');
});

// Pelayan routes
Route::middleware(['auth', 'role:pelayan'])->prefix('pelayan')->name('pelayan.')->group(function () {
    Route::get('/dashboard', [PelayanDashboardController::class, 'index'])->name('dashboard');
    Route::resource('/orders', PelayanOrderController::class);
    Route::post('/orders/{order}/cancel', [PelayanOrderController::class, 'cancel'])->name('orders.cancel');
});

// Dapur routes
Route::middleware(['auth', 'role:dapur'])->prefix('dapur')->name('dapur.')->group(function () {
    Route::get('/dashboard', [DapurDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [KitchenOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [KitchenOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/accept', [KitchenOrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/process', [KitchenOrderController::class, 'process'])->name('orders.process');
    Route::post('/orders/{order}/ready', [KitchenOrderController::class, 'ready'])->name('orders.ready');
});

// SSO (Google Sim) Routes
Route::get('/sso/google', [App\Http\Controllers\SSOController::class, 'index'])->name('sso.google');
Route::match(['get', 'post'], '/sso/google/callback', [App\Http\Controllers\SSOController::class, 'callback'])->name('sso.google.callback')->middleware('throttle:login');
Route::get('/sso/google/register', [App\Http\Controllers\SSOController::class, 'showRegister'])->name('sso.google.register');
Route::post('/sso/google/register', [App\Http\Controllers\SSOController::class, 'register'])->name('sso.google.register.process')->middleware('throttle:login');
