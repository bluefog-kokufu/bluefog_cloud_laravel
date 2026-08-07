<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Notice;
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

        Notice::create([
            'published_at' => '2026-07-23',
            'title' => '操作マニュアルのお知らせ',
            'content' => 'ユーザー利用マニュアルはこちら',
            'link' => 'manual.html',
            'pdf_link' => 'manual.pdf',
        ]);

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
                'reg_no' => 'T1234567890123',
                'zip' => '600-0000',
                'addr' => '京都府京都市中京区〇〇町1-2-3',
                'tel' => '075-000-0000',
                'bank' => '〇〇銀行 △△支店 普通 1234567 ユーザーキギヨウ(カ',
            ]
        );

        Notice::create([
            'published_at' => '2026-07-01',
            'title' => '電子帳簿保存法対応:タイムスタンプ付与機能を更新しました。',
        ]);

        Notice::create([
            'published_at' => '2026-06-15',
            'title' => 'インボイス(適格請求書)テンプレートを更新しました。',
        ]);

        Notice::create([
            'published_at' => '2026-05-10',
            'title' => '財務三表のCSV保存機能を追加しました。',
        ]);
    }
}
