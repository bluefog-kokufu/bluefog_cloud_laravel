<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

/**
 * 本番テナント用の最小seeder。DatabaseSeeder(ローカル開発用のサンプル顧客・売上・財務諸表等)とは異なり、
 * アプリが前提とする自社情報(Company、1行のみ)だけを作成する。会社名はテナントの.envのAPP_NAMEを流用し、
 * 税率・住所・銀行口座などは契約企業自身が「会計・消費税設定」画面で入力する想定
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        if (Company::query()->exists()) {
            return;
        }

        Company::create([
            'name' => config('app.name'),
        ]);
    }
}
