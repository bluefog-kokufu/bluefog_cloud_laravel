<?php

namespace Database\Seeders;

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
