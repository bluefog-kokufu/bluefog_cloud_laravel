<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Notice;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_notice_list(): void
    {
        Notice::create([
            'published_at' => '2026-07-23',
            'title' => '操作マニュアルのお知らせ',
            'content' => 'ユーザー利用マニュアルはこちら',
            'link' => 'manual.html',
            'pdf_link' => 'manual.pdf',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('お知らせ');
        $response->assertSee('操作マニュアルのお知らせ');
    }

    public function test_dashboard_shows_aggregated_stat_cards(): void
    {
        $customer = Customer::create(['name' => 'テスト取引先株式会社', 'type' => '受注取引管理']);

        $thisMonth = now()->format('Y-m');
        Sale::create(['date' => "{$thisMonth}-05", 'cust_id' => $customer->id, 'amount' => 100000, 'tax' => 10000, 'status' => '未請求']);
        Sale::create(['date' => "{$thisMonth}-10", 'cust_id' => $customer->id, 'amount' => 50000, 'tax' => 5000, 'status' => '入金済']);
        Sale::create(['date' => '2020-01-10', 'cust_id' => $customer->id, 'amount' => 999999, 'tax' => 99999, 'status' => '未請求']);

        Purchase::create(['date' => "{$thisMonth}-05", 'cust_id' => $customer->id, 'amount' => 30000, 'tax' => 3000, 'status' => '未払い']);
        Purchase::create(['date' => "{$thisMonth}-06", 'cust_id' => $customer->id, 'amount' => 20000, 'tax' => 2000, 'status' => '支払い済']);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        // 登録顧客数
        $response->assertSee('1 社');
        // 今月の売上(税抜) = 100,000 + 50,000（当月分のみ、税抜）
        $response->assertSee('¥150,000');
        // 未回収売掛金(税込) = 入金済以外の合計（(100,000+10,000) + (999,999+99,999)）
        $response->assertSee('¥1,209,998');
        // 未払買掛金(税込) = 支払い済以外の合計（30,000+3,000）
        $response->assertSee('¥33,000');
    }

    public function test_notice_management_link_moved_to_side_menu(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(route('admin.notices.index'), false);

        $quickMenuHtml = preg_match('/クイックメニュー.*?<\/div>\s*<\/div>/s', $response->getContent(), $matches);
        $this->assertSame(1, $quickMenuHtml);
        $this->assertStringNotContainsString('お知らせ管理', $matches[0]);
    }
}
