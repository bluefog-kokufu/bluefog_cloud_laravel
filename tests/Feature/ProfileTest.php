<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_profile_and_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('プロフィール編集')
            ->assertSee('パスワード変更');

        $this->actingAs($user)
            ->from(route('profile'))
            ->post(route('profile.update'), [
                'name' => 'New Name',
                'email' => 'new@example.com',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHas('profile_success', '更新しました。');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);

        $this->actingAs($user)
            ->from(route('profile'))
            ->post(route('profile.password'), [
                'current_password' => 'secret123',
                'new_password' => 'newpass123',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHas('password_success', 'パスワードを変更しました。');

        $this->assertTrue(Hash::check('newpass123', $user->fresh()->password));
    }
}
