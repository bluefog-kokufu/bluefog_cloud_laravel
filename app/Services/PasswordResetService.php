<?php

namespace App\Services;

use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Repositories\PasswordResetTokenRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetService
{
    /** トークンの有効期限（分） */
    public const EXPIRY_MINUTES = 60;

    public function __construct(private readonly PasswordResetTokenRepositoryInterface $tokens) {}

    /**
     * メールアドレスが登録済みであれば再設定用トークンを発行し、メールを送信する
     */
    public function sendResetLink(string $email): void
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            return;
        }

        $token = Str::random(64);
        $this->tokens->createOrUpdate($email, $token);

        Mail::to($email)->send(new PasswordResetMail($email, $token));
    }

    /**
     * トークンの状態を判定する
     *
     * @return 'valid'|'expired'|'invalid'
     */
    public function checkToken(string $email, string $token): string
    {
        $record = $this->tokens->findByEmail($email);

        if (! $record || ! hash_equals($record->token, $token)) {
            return 'invalid';
        }

        if ($record->created_at->addMinutes(self::EXPIRY_MINUTES)->isPast()) {
            return 'expired';
        }

        return 'valid';
    }

    /**
     * トークンを検証した上でパスワードを再設定し、使用済みトークンを削除する
     */
    public function resetPassword(string $email, string $token, string $password): bool
    {
        if ($this->checkToken($email, $token) !== 'valid') {
            return false;
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            return false;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->tokens->deleteByEmail($email);

        return true;
    }
}
