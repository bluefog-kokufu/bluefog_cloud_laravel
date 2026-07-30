<?php

namespace Tests\Feature;

use App\Models\Notice;
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
}
