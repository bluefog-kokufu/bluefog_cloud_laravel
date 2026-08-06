<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CustomerController;
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
    Route::resource('admin/notices', \App\Http\Controllers\Admin\NoticeController::class)->names('admin.notices');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/customer/import', [CustomerController::class, 'import'])->name('customer.import');
    Route::get('/customer/export', [CustomerController::class, 'export'])->name('customer.export');
    Route::get('/customer/template', [CustomerController::class, 'template'])->name('customer.template');
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
});
