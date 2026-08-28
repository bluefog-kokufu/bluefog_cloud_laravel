<?php

namespace Tests\Feature;

use App\Models\BalanceSheet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceSheetAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_balance_sheet_with_totals(): void
    {
        $user = User::factory()->create();
        BalanceSheet::create([
            'date' => '2026-08-16',
            'assets' => [['name' => '現金及び預金', 'v' => 3550000]],
            'liabs' => [['name' => '買掛金', 'v' => 980000]],
            'equity' => [['name' => '資本金', 'v' => 2570000]],
        ]);

        $response = $this->actingAs($user)->get(route('bs'));
        $response->assertOk();
        $response->assertSee('3,550,000', false);
        $response->assertSee('貸借一致しています', false);
    }

    public function test_editable_amount_inputs_are_comma_formatted(): void
    {
        $user = User::factory()->create();
        BalanceSheet::create([
            'date' => '2026-08-16',
            'assets' => [['name' => '現金及び預金', 'v' => 3550000]],
            'liabs' => [],
            'equity' => [],
        ]);

        $response = $this->actingAs($user)->get(route('bs'));
        $response->assertOk();
        $response->assertSee('value="3,550,000"', false);
        $response->assertDontSee('value="3550000"', false);
    }

    public function test_admin_can_submit_comma_formatted_amounts(): void
    {
        $user = User::factory()->create();
        $balanceSheet = BalanceSheet::create(['date' => '2026-08-16', 'assets' => [], 'liabs' => [], 'equity' => []]);

        $response = $this->actingAs($user)->put(route('bs.update'), [
            'date' => '2026-08-31',
            'assets' => [['name' => '現金及び預金', 'v' => '3,550,000']],
            'liabs' => [['name' => '買掛金', 'v' => '980,000']],
            'equity' => [],
        ]);

        $response->assertRedirect(route('bs'));
        $balanceSheet->refresh();
        $this->assertSame(3550000, $balanceSheet->assets[0]['v']);
        $this->assertSame(980000, $balanceSheet->liabs[0]['v']);
    }

    public function test_admin_can_update_balance_sheet_rows(): void
    {
        $user = User::factory()->create();
        $balanceSheet = BalanceSheet::create([
            'date' => '2026-08-16',
            'assets' => [['name' => '現金及び預金', 'v' => 1000]],
            'liabs' => [],
            'equity' => [['name' => '資本金', 'v' => 1000]],
        ]);

        $response = $this->actingAs($user)->put(route('bs.update'), [
            'date' => '2026-08-31',
            'assets' => [['name' => '現金及び預金', 'v' => 2000]],
            'liabs' => [['name' => '買掛金', 'v' => 500]],
            'equity' => [['name' => '資本金', 'v' => 1500]],
        ]);

        $response->assertRedirect(route('bs'));
        $balanceSheet->refresh();
        $this->assertSame('2026-08-31', $balanceSheet->date->format('Y-m-d'));
        $this->assertSame(2000, $balanceSheet->assets[0]['v']);
        $this->assertCount(1, $balanceSheet->liabs);
    }

    public function test_balance_sheet_update_requires_date(): void
    {
        $user = User::factory()->create();
        BalanceSheet::create(['date' => '2026-08-16', 'assets' => [], 'liabs' => [], 'equity' => []]);

        $response = $this->actingAs($user)->put(route('bs.update'), [
            'assets' => [],
            'liabs' => [],
            'equity' => [],
        ]);

        $response->assertRedirect(route('bs'));
        $response->assertSessionHasErrors(['date']);
    }

    public function test_balance_sheet_csv_export(): void
    {
        $user = User::factory()->create();
        BalanceSheet::create(['date' => '2026-08-16', 'assets' => [], 'liabs' => [], 'equity' => []]);

        $response = $this->actingAs($user)->get(route('bs.export'));
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
