<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_settings(): void
    {
        $user = User::factory()->create();
        Company::create([
            'name' => 'テスト企業株式会社',
            'tax_rate' => 10,
            'rounding' => 'floor',
            'reg_no' => 'T1234567890123',
            'zip' => '600-0000',
            'addr' => '京都府京都市中京区〇〇町1-2-3',
            'tel' => '075-000-0000',
            'bank' => '〇〇銀行 △△支店 普通 1234567',
        ]);

        $response = $this->actingAs($user)->get(route('settings'));
        $response->assertOk();
        $response->assertSee('テスト企業株式会社', false);
        $response->assertSee('会計 / 端数・消費税設定', false);
    }

    public function test_admin_can_update_settings(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'name' => 'テスト企業株式会社',
            'tax_rate' => 10,
            'rounding' => 'floor',
        ]);

        $response = $this->actingAs($user)->put(route('settings.update'), [
            'tax_rate' => 8,
            'rounding' => 'round',
            'name' => '更新後企業株式会社',
            'reg_no' => 'T9999999999999',
            'zip' => '100-0001',
            'tel' => '03-0000-0000',
            'addr' => '東京都千代田区1-1-1',
            'bank' => '△△銀行 本店 普通 7654321',
        ]);

        $response->assertRedirect(route('settings'));
        $company->refresh();
        $this->assertSame(8, $company->tax_rate);
        $this->assertSame('round', $company->rounding);
        $this->assertSame('更新後企業株式会社', $company->name);
        $this->assertSame('東京都千代田区1-1-1', $company->addr);
    }

    public function test_settings_update_requires_valid_tax_rate_and_name(): void
    {
        $user = User::factory()->create();
        Company::create(['name' => 'テスト企業株式会社', 'tax_rate' => 10, 'rounding' => 'floor']);

        $response = $this->actingAs($user)->put(route('settings.update'), [
            'tax_rate' => 5,
            'rounding' => 'floor',
            'name' => '',
        ]);

        $response->assertRedirect(route('settings'));
        $response->assertSessionHasErrors(['tax_rate', 'name']);
    }
}
