<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordResetMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_no_longer_shows_signup_link(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('会員登録はこちら')
            ->assertSee('パスワードを忘れた方');
    }

    public function test_user_can_request_and_complete_password_reset(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $this->post(route('password.forgot.send'), ['email' => 'reset@example.com'])
            ->assertRedirect(route('password.forgot'))
            ->assertSessionHas('status');

        Mail::assertSent(PasswordResetMail::class, fn ($mail) => $mail->email === 'reset@example.com');

        $token = PasswordResetToken::query()->find('reset@example.com');
        $this->assertNotNull($token);

        $this->get(route('password.reset', ['email' => 'reset@example.com', 'token' => $token->token]))
            ->assertOk()
            ->assertSee('新しいパスワードを設定');

        $this->post(route('password.reset.submit'), [
            'email' => 'reset@example.com',
            'token' => $token->token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
        $this->assertNull(PasswordResetToken::query()->find('reset@example.com'));
    }

    public function test_expired_token_shows_timeout_screen(): void
    {
        $user = User::factory()->create(['email' => 'expired@example.com']);

        PasswordResetToken::query()->create([
            'email' => 'expired@example.com',
            'token' => 'expired-token',
            'created_at' => now()->subMinutes(61),
        ]);

        $this->get(route('password.reset', ['email' => 'expired@example.com', 'token' => 'expired-token']))
            ->assertOk()
            ->assertSee('タイムアウトしました');

        $this->post(route('password.reset.submit'), [
            'email' => 'expired@example.com',
            'token' => 'expired-token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertSee('タイムアウトしました');

        $this->assertFalse(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_invalid_token_shows_invalid_screen(): void
    {
        $this->get(route('password.reset', ['email' => 'nouser@example.com', 'token' => 'wrong-token']))
            ->assertOk()
            ->assertSee('URLが無効です');
    }

    public function test_sending_reset_link_for_unknown_email_does_not_leak_existence(): void
    {
        Mail::fake();

        $this->post(route('password.forgot.send'), ['email' => 'unknown@example.com'])
            ->assertRedirect(route('password.forgot'))
            ->assertSessionHas('status');

        Mail::assertNotSent(PasswordResetMail::class);
        $this->assertNull(PasswordResetToken::query()->find('unknown@example.com'));
    }
}
