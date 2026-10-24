<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Merchant;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    // Portal Kantor (Customer) Login
    Route::get('/login', [LoginController::class, 'createCustomer'])->name('login');
    Route::post('/login', [LoginController::class, 'storeCustomer']);
    Route::get('/kantor/login', [LoginController::class, 'createCustomer'])->name('customer.login');
    Route::post('/kantor/login', [LoginController::class, 'storeCustomer']);

    // Portal Katering (Merchant) Login
    Route::get('/merchant/login', [LoginController::class, 'createMerchant'])->name('merchant.login');
    Route::post('/merchant/login', [LoginController::class, 'storeMerchant']);
    Route::get('/login/katering', [LoginController::class, 'createMerchant']);
    Route::post('/login/katering', [LoginController::class, 'storeMerchant']);

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Shared: both parties of an order open the same invoice.
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'download'])->name('invoices.pdf');
    Route::patch('/invoices/{invoice}/paid', [InvoiceController::class, 'markPaid'])->name('invoices.paid');

    Route::middleware('role:merchant')->prefix('merchant')->name('merchant.')->group(function () {
        Route::get('/', Merchant\DashboardController::class)->name('dashboard');
        Route::get('/profil', [Merchant\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [Merchant\ProfileController::class, 'update'])->name('profile.update');
        Route::resource('menu', Merchant\MenuController::class)
            ->except('show')
            ->parameters(['menu' => 'menu'])
            ->names('menus');
        Route::get('/pesanan', [Merchant\OrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/{order}', [Merchant\OrderController::class, 'show'])->name('orders.show');
        Route::patch('/pesanan/{order}/status', [Merchant\OrderController::class, 'updateStatus'])->name('orders.status');
    });

    Route::middleware('role:customer')->prefix('kantor')->name('customer.')->group(function () {
        Route::get('/katering', [Customer\CateringController::class, 'index'])->name('caterings.index');
        Route::get('/katering/{merchant}', [Customer\CateringController::class, 'show'])->name('caterings.show');
        Route::post('/katering/{merchant}/pesan', [Customer\OrderController::class, 'store'])->name('orders.store');
        Route::get('/pesanan', [Customer\OrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/{order}', [Customer\OrderController::class, 'show'])->name('orders.show');
        Route::patch('/pesanan/{order}/batal', [Customer\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/profil', [Customer\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [Customer\ProfileController::class, 'update'])->name('profile.update');
    });
});
