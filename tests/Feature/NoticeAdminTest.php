<?php

namespace Tests\Feature;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoticeAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_delete_notice(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.notices.store'), [
            'published_at' => '2026-08-01',
            'title' => '管理画面テスト',
            'content' => '追加テスト',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.notices.index'));
        $this->assertDatabaseHas('notices', ['title' => '管理画面テスト']);

        $notice = Notice::where('title', '管理画面テスト')->firstOrFail();

        $deleteResponse = $this->actingAs($user)->delete(route('admin.notices.destroy', $notice));
        $deleteResponse->assertRedirect(route('admin.notices.index'));
        $this->assertDatabaseMissing('notices', ['id' => $notice->id]);
    }
}
