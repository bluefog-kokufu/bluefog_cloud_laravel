<?php

namespace App\Providers;

use App\Repositories\BalanceSheetRepository;
use App\Repositories\BalanceSheetRepositoryInterface;
use App\Repositories\CashFlowStatementRepository;
use App\Repositories\CashFlowStatementRepositoryInterface;
use App\Repositories\CompanyRepository;
use App\Repositories\CompanyRepositoryInterface;
use App\Repositories\IncomeStatementRepository;
use App\Repositories\IncomeStatementRepositoryInterface;
use App\Repositories\LedgerEntryRepository;
use App\Repositories\LedgerEntryRepositoryInterface;
use App\Repositories\PasswordResetTokenRepository;
use App\Repositories\PasswordResetTokenRepositoryInterface;
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
        $this->app->bind(BalanceSheetRepositoryInterface::class, BalanceSheetRepository::class);
        $this->app->bind(IncomeStatementRepositoryInterface::class, IncomeStatementRepository::class);
        $this->app->bind(CashFlowStatementRepositoryInterface::class, CashFlowStatementRepository::class);
        $this->app->bind(LedgerEntryRepositoryInterface::class, LedgerEntryRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(PasswordResetTokenRepositoryInterface::class, PasswordResetTokenRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
