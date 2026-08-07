<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\PaymentNotice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentNoticeAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_payment_notice_with_items_and_number_is_auto_issued(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'テスト取引先株式会社', 'type' => '受注取引管理']);

        $response = $this->actingAs($user)->post(route('paynotice.store'), [
            'cust_id' => $customer->id,
            'pay_date' => '2026-08-31',
            'title' => '8月分お支払いのご案内',
            'items' => [
                ['date' => '2026-08-01', 'item' => '業務委託料', 'price' => 120000, 'unit' => '式', 'qty' => 1, 'tax' => '10%'],
                ['date' => '2026-08-01', 'item' => '交通費実費', 'price' => 3200, 'unit' => '式', 'qty' => 1, 'tax' => '非課税'],
            ],
        ]);

        $response->assertRedirect(route('paynotice'));
        $notice = PaymentNotice::where('cust_id', $customer->id)->firstOrFail();
        $this->assertTrue(Str::startsWith($notice->id, 'SC-'.now()->format('Ymd').'-'));
        $this->assertCount(2, $notice->items);
    }

    public function test_payment_notice_creation_requires_customer_and_at_least_one_item(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('paynotice.store'), [
            'pay_date' => '2026-08-31',
            'title' => '8月分お支払いのご案内',
            'items' => [],
        ]);

        $response->assertRedirect(route('paynotice'));
        $response->assertSessionHasErrors(['cust_id', 'items']);
        $this->assertDatabaseCount('payment_notices', 0);
    }

    public function test_admin_can_update_and_delete_payment_notice(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'テスト取引先株式会社', 'type' => '受注取引管理']);
        $notice = PaymentNotice::create([
            'id' => 'SC-20260801-001',
            'cust_id' => $customer->id,
            'title' => '8月分お支払いのご案内',
            'pay_date' => '2026-08-31',
            'items' => [['date' => '2026-08-01', 'item' => '業務委託料', 'price' => 120000, 'unit' => '式', 'qty' => 1, 'tax' => '10%']],
        ]);

        $updateResponse = $this->actingAs($user)->put(route('paynotice.update', $notice), [
            'cust_id' => $customer->id,
            'pay_date' => '2026-09-30',
            'title' => '9月分お支払いのご案内',
            'items' => [
                ['date' => '2026-09-01', 'item' => '業務委託料(更新)', 'price' => 150000, 'unit' => '式', 'qty' => 1, 'tax' => '10%'],
            ],
        ]);

        $updateResponse->assertRedirect(route('paynotice'));
        $notice->refresh();
        $this->assertSame('9月分お支払いのご案内', $notice->title);
        $this->assertCount(1, $notice->items);

        $deleteResponse = $this->actingAs($user)->delete(route('paynotice.destroy', $notice));
        $deleteResponse->assertOk();
        $this->assertDatabaseMissing('payment_notices', ['id' => $notice->id]);
    }

    public function test_admin_can_view_payment_notice_document(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'テスト取引先株式会社', 'type' => '受注取引管理']);
        $notice = PaymentNotice::create([
            'id' => 'SC-20260801-001',
            'cust_id' => $customer->id,
            'title' => '8月分お支払いのご案内',
            'pay_date' => '2026-08-31',
            'items' => [['date' => '2026-08-01', 'item' => '業務委託料', 'price' => 120000, 'unit' => '式', 'qty' => 1, 'tax' => '10%']],
        ]);

        $viewResponse = $this->actingAs($user)->get(route('paynotice.view', $notice));
        $viewResponse->assertOk();
        $viewResponse->assertSee('支払通知書', false);
        $viewResponse->assertSee('132,000', false);
    }

    public function test_payment_notice_index_shows_total_amount(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'テスト取引先株式会社', 'type' => '受注取引管理']);
        PaymentNotice::create([
            'id' => 'SC-20260801-001',
            'cust_id' => $customer->id,
            'title' => '8月分お支払いのご案内',
            'pay_date' => '2026-08-31',
            'items' => [['date' => '2026-08-01', 'item' => '業務委託料', 'price' => 120000, 'unit' => '式', 'qty' => 1, 'tax' => '10%']],
        ]);

        $response = $this->actingAs($user)->get(route('paynotice'));
        $response->assertOk();
        $response->assertSee('132,000', false);
    }
}
