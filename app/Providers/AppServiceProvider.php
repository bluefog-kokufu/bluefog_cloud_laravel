<?php

namespace App\Providers;

use App\Repositories\PaymentNoticeRepository;
use App\Repositories\PaymentNoticeRepositoryInterface;
use App\Repositories\PurchaseRepository;
use App\Repositories\PurchaseRepositoryInterface;
use App\Repositories\SaleRepository;
use App\Repositories\SaleRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SaleRepositoryInterface::class, SaleRepository::class);
        $this->app->bind(PurchaseRepositoryInterface::class, PurchaseRepository::class);
        $this->app->bind(PaymentNoticeRepositoryInterface::class, PaymentNoticeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
