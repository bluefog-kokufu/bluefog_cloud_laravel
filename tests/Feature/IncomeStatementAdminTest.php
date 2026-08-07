<?php

namespace Tests\Feature;

use App\Models\IncomeStatement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeStatementAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_income_statement_with_profit(): void
    {
        $user = User::factory()->create();
        IncomeStatement::create([
            'period_from' => '2026-01-01',
            'period_to' => '2026-12-31',
            'rows' => [
                ['name' => '売上高', 'type' => '収益', 'v' => 1000000],
                ['name' => '売上原価', 'type' => '費用', 'v' => 400000],
            ],
        ]);

        $response = $this->actingAs($user)->get(route('pl'));
        $response->assertOk();
        $response->assertSee('1,000,000', false);
        $response->assertSee('600,000', false);
    }

    public function test_admin_can_update_income_statement_rows(): void
    {
        $user = User::factory()->create();
        $incomeStatement = IncomeStatement::create([
            'period_from' => '2026-01-01',
            'period_to' => '2026-12-31',
            'rows' => [],
        ]);

        $response = $this->actingAs($user)->put(route('pl.update'), [
            'period_from' => '2026-01-01',
            'period_to' => '2026-12-31',
            'rows' => [
                ['name' => '売上高', 'type' => '収益', 'v' => 500000],
                ['name' => '販管費', 'type' => '費用', 'v' => 200000],
            ],
        ]);

        $response->assertRedirect(route('pl'));
        $incomeStatement->refresh();
        $this->assertCount(2, $incomeStatement->rows);
    }

    public function test_income_statement_update_requires_valid_period(): void
    {
        $user = User::factory()->create();
        IncomeStatement::create(['period_from' => '2026-01-01', 'period_to' => '2026-12-31', 'rows' => []]);

        $response = $this->actingAs($user)->put(route('pl.update'), [
            'period_from' => '2026-12-31',
            'period_to' => '2026-01-01',
            'rows' => [],
        ]);

        $response->assertRedirect(route('pl'));
        $response->assertSessionHasErrors(['period_to']);
    }

    public function test_income_statement_csv_export(): void
    {
        $user = User::factory()->create();
        IncomeStatement::create(['period_from' => '2026-01-01', 'period_to' => '2026-12-31', 'rows' => []]);

        $response = $this->actingAs($user)->get(route('pl.export'));
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
