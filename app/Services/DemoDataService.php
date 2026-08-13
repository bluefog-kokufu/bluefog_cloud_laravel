<?php

namespace App\Services;

use App\Models\BalanceSheet;
use App\Models\CashFlowStatement;
use App\Models\Company;
use App\Models\Customer;
use App\Models\IncomeStatement;
use App\Models\LedgerEntry;
use App\Models\PaymentNotice;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * 「デモデータを初期化」機能。取引・帳簿データを全消去してDatabaseSeederのサンプルデータへ戻す。
 * 実データを消去する破壊的な操作のため、本番稼働中のテナント(APP_ENV=production)では
 * SettingsController側で無効化している
 */
class DemoDataService
{
    public function reset(): void
    {
        DB::transaction(function () {
            SaleItem::query()->delete();
            Sale::query()->delete();
            Purchase::query()->delete();
            PaymentNotice::query()->delete();
            LedgerEntry::query()->delete();
            BalanceSheet::query()->delete();
            IncomeStatement::query()->delete();
            CashFlowStatement::query()->delete();
            Customer::withTrashed()->forceDelete();
            Company::query()->delete();
        });

        // 発注取引に添付された書類ファイルも削除する(DB上のレコードは既に消えている)
        File::deleteDirectory(storage_path('app/private/purchases'));

        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder', '--force' => true]);
    }
}
