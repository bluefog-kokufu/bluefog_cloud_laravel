<?php

namespace Tests\Feature;

use App\Models\LedgerEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LedgerAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_journal_tab_with_totals(): void
    {
        $user = User::factory()->create();
        LedgerEntry::create([
            'no' => 'test1', 'year' => '2026', 'm' => '5', 'd' => '11',
            'dr_acct' => '売掛金', 'dr_amt' => 55000, 'cr_acct' => '売上高', 'cr_amt' => 55000,
            'note' => '商品売上', 'page' => '1',
        ]);

        $response = $this->actingAs($user)->get(route('ledger'));
        $response->assertOk();
        $response->assertSee('仕訳帳', false);
        $response->assertSee('55,000', false);
    }

    public function test_admin_can_view_ledger_tab_grouped_by_account_with_running_balance(): void
    {
        $user = User::factory()->create();
        LedgerEntry::create([
            'no' => 'a1', 'year' => '2026', 'm' => '5', 'd' => '1',
            'dr_acct' => '現金', 'dr_amt' => 10000, 'cr_acct' => '売上高', 'cr_amt' => 10000,
            'note' => '売上', 'page' => '1',
        ]);
        LedgerEntry::create([
            'no' => 'a2', 'year' => '2026', 'm' => '5', 'd' => '2',
            'dr_acct' => '消耗品費', 'dr_amt' => 3000, 'cr_acct' => '現金', 'cr_amt' => 3000,
            'note' => '文具購入', 'page' => '1',
        ]);

        $response = $this->actingAs($user)->get(route('ledger', ['tab' => 'lg']));
        $response->assertOk();
        $response->assertSee('勘定科目: 現金', false);
        $response->assertSee('7,000', false);
    }

    public function test_admin_can_save_journal_rows(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('ledger.update'), [
            'rows' => [
                ['no' => 'b1', 'year' => '2026', 'm' => '6', 'd' => '1', 'dr_acct' => '現金', 'dr_amt' => 20000, 'cr_acct' => '売上高', 'cr_amt' => 20000, 'note' => 'テスト', 'page' => '1'],
            ],
        ]);

        $response->assertRedirect(route('ledger'));
        $this->assertDatabaseHas('ledger_entries', ['no' => 'b1', 'dr_amt' => 20000]);
    }

    public function test_saving_journal_replaces_all_existing_rows(): void
    {
        $user = User::factory()->create();
        LedgerEntry::create([
            'no' => 'old', 'year' => '2026', 'm' => '1', 'd' => '1',
            'dr_acct' => '現金', 'dr_amt' => 1000, 'cr_acct' => '売上高', 'cr_amt' => 1000,
        ]);

        $response = $this->actingAs($user)->put(route('ledger.update'), [
            'rows' => [
                ['no' => 'new', 'year' => '2026', 'm' => '2', 'd' => '2', 'dr_acct' => '現金', 'dr_amt' => 2000, 'cr_acct' => '売上高', 'cr_amt' => 2000],
            ],
        ]);

        $response->assertRedirect(route('ledger'));
        $this->assertDatabaseMissing('ledger_entries', ['no' => 'old']);
        $this->assertDatabaseHas('ledger_entries', ['no' => 'new']);
        $this->assertSame(1, LedgerEntry::count());
    }

    public function test_ledger_csv_import_adds_rows(): void
    {
        $user = User::factory()->create();

        $csv = "伝票No.,年,月,日,借方勘定科目,金額,貸方勘定科目,金額,勘定科目,摘要,仕丁,借方,貸方,残高\n"
            ."c1,2026,7,1,現金,50000,売上高,50000,現金／売上高,CSV取込,1,50000,50000,\n";
        $file = UploadedFile::fake()->createWithContent('ledger.csv', $csv);

        $response = $this->actingAs($user)->post(route('ledger.import'), ['csv_file' => $file]);

        $response->assertRedirect(route('ledger'));
        $this->assertDatabaseHas('ledger_entries', ['no' => 'c1', 'dr_amt' => 50000]);
    }

    public function test_ledger_csv_export(): void
    {
        $user = User::factory()->create();
        LedgerEntry::create([
            'no' => 'test1', 'year' => '2026', 'm' => '5', 'd' => '11',
            'dr_acct' => '売掛金', 'dr_amt' => 55000, 'cr_acct' => '売上高', 'cr_amt' => 55000,
        ]);

        $response = $this->actingAs($user)->get(route('ledger.export'));
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
