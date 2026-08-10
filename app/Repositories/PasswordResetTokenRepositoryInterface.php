<?php

namespace App\Repositories;

use App\Models\PasswordResetToken;

interface PasswordResetTokenRepositoryInterface
{
    /**
     * 指定メールアドレスのトークンを発行する（既存のトークンは上書きする）
     */
    public function createOrUpdate(string $email, string $token): PasswordResetToken;

    /**
     * メールアドレスに紐づくトークンを取得する
     */
    public function findByEmail(string $email): ?PasswordResetToken;

    /**
     * メールアドレスに紐づくトークンを削除する
     */
    public function deleteByEmail(string $email): void;
}
