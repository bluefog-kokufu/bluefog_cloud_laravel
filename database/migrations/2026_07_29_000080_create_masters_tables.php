<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMastersTables extends Migration
{
    public function up()
    {
        Schema::create('m_landlords', function (Blueprint $table) {
            $table->comment('賃貸革命連携画面で利用する家主基本情報');
            $table->string('id')->primary()->comment('家主ID');
            $table->string('name')->comment('家主名');
            $table->string('contact')->nullable()->comment('担当者名');
            $table->string('email')->nullable()->comment('連絡先メールアドレス');
            $table->string('tel')->nullable()->comment('電話番号');
            $table->text('addr')->nullable()->comment('住所');
            $table->text('memo')->nullable()->comment('メモ');
            $table->timestamps();
        });

        Schema::create('m_contractors', function (Blueprint $table) {
            $table->comment('賃貸革命連携画面で利用する契約者情報');
            $table->string('id')->primary()->comment('契約者ID');
            $table->string('name')->comment('契約者名');
            $table->string('contact')->nullable()->comment('担当者名');
            $table->string('email')->nullable()->comment('連絡先メールアドレス');
            $table->string('tel')->nullable()->comment('電話番号');
            $table->text('addr')->nullable()->comment('住所');
            $table->text('memo')->nullable()->comment('メモ');
            $table->timestamps();
        });

        Schema::create('m_repairers', function (Blueprint $table) {
            $table->comment('賃貸革命連携画面で利用する修繕業者情報');
            $table->string('id')->primary()->comment('修繕業者ID');
            $table->string('name')->comment('業者名');
            $table->string('contact')->nullable()->comment('担当者名');
            $table->string('email')->nullable()->comment('連絡先メールアドレス');
            $table->string('tel')->nullable()->comment('電話番号');
            $table->text('addr')->nullable()->comment('住所');
            $table->text('memo')->nullable()->comment('メモ');
            $table->timestamps();
        });

        Schema::create('m_agents', function (Blueprint $table) {
            $table->comment('賃貸革命連携画面で利用する仲介・管理業者情報');
            $table->string('id')->primary()->comment('業者ID');
            $table->string('name')->comment('業者名');
            $table->string('contact')->nullable()->comment('担当者名');
            $table->string('email')->nullable()->comment('連絡先メールアドレス');
            $table->string('tel')->nullable()->comment('電話番号');
            $table->text('addr')->nullable()->comment('住所');
            $table->text('memo')->nullable()->comment('メモ');
            $table->timestamps();
        });

        Schema::create('m_insurers', function (Blueprint $table) {
            $table->comment('賃貸革命連携画面で利用する保険会社情報');
            $table->string('id')->primary()->comment('保険会社ID');
            $table->string('name')->comment('会社名');
            $table->string('contact')->nullable()->comment('担当者名');
            $table->string('email')->nullable()->comment('連絡先メールアドレス');
            $table->string('tel')->nullable()->comment('電話番号');
            $table->text('addr')->nullable()->comment('住所');
            $table->text('memo')->nullable()->comment('メモ');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_insurers');
        Schema::dropIfExists('m_agents');
        Schema::dropIfExists('m_repairers');
        Schema::dropIfExists('m_contractors');
        Schema::dropIfExists('m_landlords');
    }
}
