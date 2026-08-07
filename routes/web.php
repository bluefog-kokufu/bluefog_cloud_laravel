<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::resource('admin/notices', NoticeController::class)->names('admin.notices');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customer/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::put('/customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
    Route::post('/customer/import', [CustomerController::class, 'import'])->name('customer.import');
    Route::get('/customer/export', [CustomerController::class, 'export'])->name('customer.export');
    Route::get('/customer/template', [CustomerController::class, 'template'])->name('customer.template');
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer');

    Route::get('/sale/create', [SaleController::class, 'create'])->name('sale.create');
    Route::post('/sale', [SaleController::class, 'store'])->name('sale.store');
    Route::get('/sale/{sale}/edit', [SaleController::class, 'edit'])->name('sale.edit');
    Route::get('/sale/{sale}/invoice', [SaleController::class, 'invoice'])->name('sale.invoice');
    Route::post('/sale/{sale}/issue', [SaleController::class, 'issue'])->name('sale.issue');
    Route::put('/sale/{sale}', [SaleController::class, 'update'])->name('sale.update');
    Route::delete('/sale/{sale}', [SaleController::class, 'destroy'])->name('sale.destroy');
    Route::post('/sale/import', [SaleController::class, 'import'])->name('sale.import');
    Route::get('/sale/export', [SaleController::class, 'export'])->name('sale.export');
    Route::get('/sale/template', [SaleController::class, 'template'])->name('sale.template');
    Route::get('/sale', [SaleController::class, 'index'])->name('sale');

    Route::get('/purchase/create', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchase.edit');
    Route::put('/purchase/{purchase}', [PurchaseController::class, 'update'])->name('purchase.update');
    Route::delete('/purchase/{purchase}', [PurchaseController::class, 'destroy'])->name('purchase.destroy');
    Route::get('/purchase/{purchase}/file/{key}', [PurchaseController::class, 'file'])
        ->whereIn('key', ['quote', 'invoice', 'receipt', 'contract'])
        ->name('purchase.file');
    Route::post('/purchase/import', [PurchaseController::class, 'import'])->name('purchase.import');
    Route::get('/purchase/export', [PurchaseController::class, 'export'])->name('purchase.export');
    Route::get('/purchase/template', [PurchaseController::class, 'template'])->name('purchase.template');
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase');
});
