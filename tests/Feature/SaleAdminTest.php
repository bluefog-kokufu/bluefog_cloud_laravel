<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SaleAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_sale_with_items_and_totals_are_calculated(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'テスト取引先株式会社', 'type' => '受注取引管理']);

        $response = $this->actingAs($user)->post(route('sale.store'), [
            'cust_id' => $customer->id,
            'date' => '2026-08-01',
            'method' => '現金',
            'status' => '未請求',
            'memo' => '',
            'items' => [
                ['name' => '保守サポート費用', 'amount' => 135000, 'rate' => 10],
                ['name' => '軽減税率商品', 'amount' => 50000, 'rate' => 8],
            ],
        ]);

        $response->assertRedirect(route('sale'));
        $sale = Sale::where('cust_id', $customer->id)->firstOrFail();
        $this->assertSame(185000, $sale->amount);
        $this->assertSame(17500, $sale->tax);
        $this->assertCount(2, $sale->items);
    }

    public function test_sale_creation_requires_customer_and_at_least_one_item(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('sale.store'), [
            'date' => '2026-08-01',
            'method' => '現金',
            'status' => '未請求',
            'items' => [],
        ]);

        $response->assertRedirect(route('sale'));
        $response->assertSessionHasErrors(['cust_id', 'items']);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_admin_can_update_and_delete_sale(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'テスト取引先株式会社', 'type' => '受注取引管理']);
        $sale = Sale::create(['date' => '2026-08-01', 'cust_id' => $customer->id, 'method' => '現金', 'status' => '未請求', 'amount' => 1000, 'tax' => 100]);
        $sale->items()->create(['name' => '商品A', 'amount' => 1000, 'rate' => 10]);

        $updateResponse = $this->actingAs($user)->put(route('sale.update', $sale), [
            'cust_id' => $customer->id,
            'date' => '2026-08-02',
            'method' => '普通預金',
            'status' => '請求済',
            'items' => [
                ['name' => '商品A(更新)', 'amount' => 2000, 'rate' => 10],
            ],
        ]);

        $updateResponse->assertRedirect(route('sale'));
        $sale->refresh();
        $this->assertSame('請求済', $sale->status);
        $this->assertSame(2000, $sale->amount);
        $this->assertSame(200, $sale->tax);
        $this->assertCount(1, $sale->items);

        $deleteResponse = $this->actingAs($user)->delete(route('sale.destroy', $sale));
        $deleteResponse->assertOk();
        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
        $this->assertDatabaseMissing('sale_items', ['sale_id' => $sale->id]);
    }

    public function test_admin_can_view_invoice_and_issue_it(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'テスト取引先株式会社', 'type' => '受注取引管理']);
        $sale = Sale::create(['date' => '2026-08-01', 'cust_id' => $customer->id, 'method' => '現金', 'status' => '未請求', 'amount' => 1000, 'tax' => 100]);
        $sale->items()->create(['name' => '商品A', 'amount' => 1000, 'rate' => 10]);

        $invoiceResponse = $this->actingAs($user)->get(route('sale.invoice', $sale));
        $invoiceResponse->assertOk();
        $invoiceResponse->assertSee('請 求 書', false);
        $this->assertNotNull($sale->fresh()->invoiced);

        $issueResponse = $this->actingAs($user)->post(route('sale.issue', $sale));
        $issueResponse->assertRedirect(route('sale'));
        $this->assertSame('請求済', $sale->fresh()->status);
    }

    public function test_csv_import_groups_rows_by_sale_number_and_creates_missing_customer(): void
    {
        $user = User::factory()->create();

        $csv = "取引No,作成日,取引先名,入金方法,品目・内容,税抜金額,税率(%),ステータス,備考\n"
            ."T001,2026-08-01,CSV顧客株式会社,現金,商品A,100000,10,未請求,\n"
            ."T001,2026-08-01,CSV顧客株式会社,現金,商品B,50000,8,未請求,\n";
        $file = UploadedFile::fake()->createWithContent('sales.csv', $csv);

        $response = $this->actingAs($user)->post(route('sale.import'), ['csv_file' => $file]);

        $response->assertRedirect(route('sale'));
        $this->assertDatabaseCount('sales', 1);
        $this->assertDatabaseHas('customers', ['name' => 'CSV顧客株式会社']);
        $sale = Sale::firstOrFail();
        $this->assertCount(2, $sale->items);
        $this->assertSame(150000, $sale->amount);
    }

    public function test_csv_export_and_template_download(): void
    {
        $user = User::factory()->create();

        $exportResponse = $this->actingAs($user)->get(route('sale.export'));
        $exportResponse->assertOk();
        $exportResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $templateResponse = $this->actingAs($user)->get(route('sale.template'));
        $templateResponse->assertOk();
    }
}
