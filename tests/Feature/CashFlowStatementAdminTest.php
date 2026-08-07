<?php

namespace Tests\Feature;

use App\Models\CashFlowStatement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashFlowStatementAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_cash_flow_statement_with_ending_balance(): void
    {
        $user = User::factory()->create();
        CashFlowStatement::create([
            'period_from' => '2026-01-01',
            'period_to' => '2026-12-31',
            'beginning_balance' => 1000000,
            'operating' => [['name' => '税引前当期純利益', 'v' => 500000]],
            'investing' => [['name' => '設備投資', 'v' => -200000]],
            'financing' => [],
        ]);

        $response = $this->actingAs($user)->get(route('cf'));
        $response->assertOk();
        $response->assertSee('1,300,000', false);
        $response->assertSee('△200,000', false);
    }

    public function test_admin_can_update_cash_flow_statement_rows(): void
    {
        $user = User::factory()->create();
        $cashFlowStatement = CashFlowStatement::create([
            'period_from' => '2026-01-01',
            'period_to' => '2026-12-31',
            'beginning_balance' => 0,
            'operating' => [],
            'investing' => [],
            'financing' => [],
        ]);

        $response = $this->actingAs($user)->put(route('cf.update'), [
            'period_from' => '2026-01-01',
            'period_to' => '2026-12-31',
            'beginning_balance' => 100000,
            'operating' => [['name' => '税引前当期純利益', 'v' => 300000]],
            'investing' => [],
            'financing' => [['name' => '借入金の返済', 'v' => -50000]],
        ]);

        $response->assertRedirect(route('cf'));
        $cashFlowStatement->refresh();
        $this->assertSame(100000, $cashFlowStatement->beginning_balance);
        $this->assertCount(1, $cashFlowStatement->financing);
    }

    public function test_cash_flow_statement_update_requires_beginning_balance(): void
    {
        $user = User::factory()->create();
        CashFlowStatement::create([
            'period_from' => '2026-01-01', 'period_to' => '2026-12-31', 'beginning_balance' => 0,
            'operating' => [], 'investing' => [], 'financing' => [],
        ]);

        $response = $this->actingAs($user)->put(route('cf.update'), [
            'period_from' => '2026-01-01',
            'period_to' => '2026-12-31',
            'operating' => [],
            'investing' => [],
            'financing' => [],
        ]);

        $response->assertRedirect(route('cf'));
        $response->assertSessionHasErrors(['beginning_balance']);
    }

    public function test_cash_flow_statement_csv_export(): void
    {
        $user = User::factory()->create();
        CashFlowStatement::create([
            'period_from' => '2026-01-01', 'period_to' => '2026-12-31', 'beginning_balance' => 0,
            'operating' => [], 'investing' => [], 'financing' => [],
        ]);

        $response = $this->actingAs($user)->get(route('cf.export'));
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
