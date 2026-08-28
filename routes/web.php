<?php

use App\Http\Controllers\Admin\BalanceSheetController;
use App\Http\Controllers\Admin\CashFlowStatementController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\IncomeStatementController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\PaymentNoticeController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\InternalBootstrapController;
use App\Http\Middleware\VerifyBootstrapToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::post('/internal/bootstrap', [InternalBootstrapController::class, 'store'])
    ->middleware(VerifyBootstrapToken::class)
    ->name('internal.bootstrap');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    Route::get('/password/forgot', [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
    Route::post('/password/forgot', [PasswordResetController::class, 'sendResetLink'])->name('password.forgot.send');
    Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [PasswordResetController::class, 'reset'])->name('password.reset.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
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

    Route::get('/paynotice/create', [PaymentNoticeController::class, 'create'])->name('paynotice.create');
    Route::get('/paynotice/next-number', [PaymentNoticeController::class, 'nextNumber'])->name('paynotice.next-number');
    Route::post('/paynotice', [PaymentNoticeController::class, 'store'])->name('paynotice.store');
    Route::get('/paynotice/{paynotice}/edit', [PaymentNoticeController::class, 'edit'])->name('paynotice.edit');
    Route::get('/paynotice/{paynotice}/view', [PaymentNoticeController::class, 'show'])->name('paynotice.view');
    Route::put('/paynotice/{paynotice}', [PaymentNoticeController::class, 'update'])->name('paynotice.update');
    Route::delete('/paynotice/{paynotice}', [PaymentNoticeController::class, 'destroy'])->name('paynotice.destroy');
    Route::get('/paynotice', [PaymentNoticeController::class, 'index'])->name('paynotice');

    Route::get('/bs/export', [BalanceSheetController::class, 'export'])->name('bs.export');
    Route::put('/bs', [BalanceSheetController::class, 'update'])->name('bs.update');
    Route::get('/bs', [BalanceSheetController::class, 'edit'])->name('bs');

    Route::get('/pl/export', [IncomeStatementController::class, 'export'])->name('pl.export');
    Route::put('/pl', [IncomeStatementController::class, 'update'])->name('pl.update');
    Route::get('/pl', [IncomeStatementController::class, 'edit'])->name('pl');

    Route::get('/cf/export', [CashFlowStatementController::class, 'export'])->name('cf.export');
    Route::put('/cf', [CashFlowStatementController::class, 'update'])->name('cf.update');
    Route::get('/cf', [CashFlowStatementController::class, 'edit'])->name('cf');

    Route::get('/ledger/export', [LedgerController::class, 'export'])->name('ledger.export');
    Route::post('/ledger/import', [LedgerController::class, 'import'])->name('ledger.import');
    Route::put('/ledger', [LedgerController::class, 'update'])->name('ledger.update');
    Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger');

    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/reset-demo', [SettingsController::class, 'resetDemo'])->name('settings.reset-demo');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
});
