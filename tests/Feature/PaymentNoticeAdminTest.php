<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\PaymentNotice;
use App\Models\User;
use App\Services\PaymentNoticeService;
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
        $noticeNo = app(PaymentNoticeService::class)->previewNoticeNo();

        $response = $this->actingAs($user)->post(route('paynotice.store'), [
            'id' => $noticeNo,
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
        $this->assertSame($noticeNo, $notice->id);
        $this->assertTrue(Str::startsWith($notice->id, 'SC-'.now()->format('Ymd').'-'));
        $this->assertCount(2, $notice->items);
    }

    public function test_create_form_displays_a_pre_issued_number(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('paynotice.create'));
        $response->assertOk();
        $response->assertSee('SC-'.now()->format('Ymd').'-001', false);
        $response->assertDontSee('保存時に自動採番されます', false);
        $response->assertSee('番号を採番し直す', false);
    }

    public function test_next_number_endpoint_bumps_the_sequence(): void
    {
        $user = User::factory()->create();

        $base = $this->actingAs($user)->getJson(route('paynotice.next-number'));
        $base->assertOk();
        $bumped = $this->actingAs($user)->getJson(route('paynotice.next-number', ['bump' => 1]));
        $bumped->assertOk();

        $this->assertSame('SC-'.now()->format('Ymd').'-001', $base->json('id'));
        $this->assertSame('SC-'.now()->format('Ymd').'-002', $bumped->json('id'));
    }

    public function test_payment_notice_creation_fails_with_a_duplicate_number(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'テスト取引先株式会社', 'type' => '受注取引管理']);
        PaymentNotice::create([
            'id' => 'SC-20260801-001',
            'cust_id' => $customer->id,
            'title' => '既存の通知書',
            'pay_date' => '2026-08-31',
            'items' => [['date' => '2026-08-01', 'item' => '業務委託料', 'price' => 1000, 'unit' => '式', 'qty' => 1, 'tax' => '10%']],
        ]);

        $response = $this->actingAs($user)->from(route('paynotice.create'))->post(route('paynotice.store'), [
            'id' => 'SC-20260801-001',
            'cust_id' => $customer->id,
            'pay_date' => '2026-08-31',
            'title' => '重複した通知書',
            'items' => [['date' => '2026-08-01', 'item' => '業務委託料', 'price' => 1000, 'unit' => '式', 'qty' => 1, 'tax' => '10%']],
        ]);

        $response->assertRedirect(route('paynotice.create'));
        $response->assertSessionHasErrors(['id']);
        $this->assertSame(1, PaymentNotice::count());
    }

    public function test_payment_notice_creation_requires_customer_and_at_least_one_item(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('paynotice.create'))->post(route('paynotice.store'), [
            'pay_date' => '2026-08-31',
            'title' => '8月分お支払いのご案内',
            'items' => [],
        ]);

        $response->assertRedirect(route('paynotice.create'));
        $response->assertSessionHasErrors(['cust_id', 'items']);
        $this->assertDatabaseCount('payment_notices', 0);
    }

    public function test_create_form_renders_as_a_full_page_not_a_modal_fragment(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('paynotice.create'));
        $response->assertOk();
        $response->assertSee('Bluefog Cloud', false);
        $response->assertSee('支払通知書一覧', false);
        $response->assertSee('支払通知書作成', false);
    }

    public function test_edit_form_renders_as_a_full_page_not_a_modal_fragment(): void
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

        $response = $this->actingAs($user)->get(route('paynotice.edit', $notice));
        $response->assertOk();
        $response->assertSee('Bluefog Cloud', false);
        $response->assertSee('支払通知書編集', false);
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
