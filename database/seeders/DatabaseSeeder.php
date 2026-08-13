<?php

namespace Database\Seeders;

use App\Models\BalanceSheet;
use App\Models\CashFlowStatement;
use App\Models\Company;
use App\Models\Customer;
use App\Models\IncomeStatement;
use App\Models\LedgerEntry;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'Bluefog Cloud Admin',
                'password' => bcrypt('password'),
            ]
        );

        Customer::firstOrCreate(
            ['id' => 'c1'],
            [
                'name' => 'テスト商事株式会社',
                'person' => '山田 太郎',
                'email' => 'info@testcompany.co.jp',
                'tel' => '03-1234-5678',
                'addr' => '東京都千代田区1-1-1',
                'site' => '月末締め翌月末払い',
                'reg_no' => 'T1234567890123',
                'memo' => 'サンプル顧客1',
            ]
        );

        Customer::firstOrCreate(
            ['id' => 'c2'],
            [
                'name' => 'サンプル株式会社',
                'person' => '佐藤 花子',
                'email' => 'sato@example.co.jp',
                'tel' => '06-9876-5432',
                'addr' => '大阪府大阪市北区2-2-2',
                'site' => '20日締め翌月10日払い',
                'reg_no' => '',
                'memo' => 'サンプル顧客2',
            ]
        );

        Customer::firstOrCreate(
            ['id' => 'c3'],
            [
                'name' => '株式会社テストソリューション',
                'person' => '鈴木 一朗',
                'email' => 'ichiro@testsoul.co.jp',
                'tel' => '052-111-2222',
                'addr' => '愛知県名古屋市中区3-3-3',
                'site' => '即時払い',
                'reg_no' => '',
                'memo' => 'サンプル顧客3',
            ]
        );

        Company::firstOrCreate(
            ['name' => 'ユーザー企業株式会社'],
            [
                'tax_rate' => 10,
                'rounding' => 'floor',
                'reg_no' => 'T1234567890123',
                'zip' => '600-0000',
                'addr' => '京都府京都市中京区〇〇町1-2-3',
                'tel' => '075-000-0000',
                'bank' => '〇〇銀行 △△支店 普通 1234567 ユーザーキギヨウ(カ',
            ]
        );

        BalanceSheet::firstOrCreate([], [
            'date' => '2026-08-16',
            'assets' => [
                ['name' => '現金及び預金', 'v' => 3550000],
                ['name' => '売掛金', 'v' => 1450000],
                ['name' => '固定資産', 'v' => 5500000],
            ],
            'liabs' => [
                ['name' => '買掛金', 'v' => 980000],
                ['name' => '長期借入金', 'v' => 3200000],
            ],
            'equity' => [
                ['name' => '資本金', 'v' => 5000000],
                ['name' => '利益剰余金', 'v' => 1320000],
            ],
        ]);

        IncomeStatement::firstOrCreate([], [
            'period_from' => '2026-01-01',
            'period_to' => '2026-12-31',
            'rows' => [
                ['name' => '売上高', 'type' => '収益', 'v' => 12800000],
                ['name' => '売上原価', 'type' => '費用', 'v' => 6400000],
                ['name' => '販売費及び一般管理費', 'type' => '費用', 'v' => 3200000],
            ],
        ]);

        CashFlowStatement::firstOrCreate([], [
            'period_from' => '2026-01-01',
            'period_to' => '2026-12-31',
            'beginning_balance' => 2100000,
            'operating' => [
                ['name' => '税引前当期純利益', 'v' => 3450000],
            ],
            'investing' => [
                ['name' => '有形固定資産の取得による支出', 'v' => -1200000],
            ],
            'financing' => [
                ['name' => '長期借入金の返済による支出', 'v' => -800000],
            ],
        ]);

        LedgerEntry::firstOrCreate(
            ['no' => 'test1'],
            [
                'year' => '2026',
                'm' => '5',
                'd' => '11',
                'dr_acct' => '売掛金',
                'dr_amt' => 55000,
                'cr_acct' => '売上高',
                'cr_amt' => 55000,
                'note' => '商品売上',
                'page' => '1',
            ]
        );
    }
}
