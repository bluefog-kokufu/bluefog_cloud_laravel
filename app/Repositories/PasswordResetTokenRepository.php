<?php

namespace App\Repositories;

use App\Models\PasswordResetToken;

class PasswordResetTokenRepository implements PasswordResetTokenRepositoryInterface
{
    public function createOrUpdate(string $email, string $token): PasswordResetToken
    {
        return PasswordResetToken::query()->updateOrCreate(
            ['email' => $email],
            ['token' => $token, 'created_at' => now()],
        );
    }

    public function findByEmail(string $email): ?PasswordResetToken
    {
        return PasswordResetToken::query()->find($email);
    }

    public function deleteByEmail(string $email): void
    {
        PasswordResetToken::query()->where('email', $email)->delete();
    }
}
