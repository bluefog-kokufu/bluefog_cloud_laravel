<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->comment('ログイン画面・ダッシュボードで利用する認証ユーザー情報');
            $table->id()->comment('ユーザーID（ログイン認証用）');
            $table->string('name')->comment('ログイン後の画面表示名');
            $table->string('email')->unique()->comment('ログイン画面で利用するメールアドレス');
            $table->timestamp('email_verified_at')->nullable()->comment('メール認証日時');
            $table->string('password')->comment('ログイン認証用パスワード');
            $table->rememberToken()->comment('ログイン状態保持用トークン');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->comment('パスワード再設定画面で利用するトークン情報');
            $table->string('email')->primary()->comment('再設定対象メールアドレス');
            $table->string('token')->comment('パスワード再設定用トークン');
            $table->timestamp('created_at')->nullable()->comment('トークン作成日時');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->comment('ログイン状態を保持するセッション情報');
            $table->string('id')->primary()->comment('セッションID');
            $table->foreignId('user_id')->nullable()->index()->comment('ログインユーザーID');
            $table->string('ip_address', 45)->nullable()->comment('ログインアクセス元IPアドレス');
            $table->text('user_agent')->nullable()->comment('利用端末情報');
            $table->longText('payload')->comment('セッション内容');
            $table->integer('last_activity')->index()->comment('最後のアクセス時刻');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
