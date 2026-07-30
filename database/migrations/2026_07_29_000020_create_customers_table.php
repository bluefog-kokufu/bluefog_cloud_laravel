<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->comment('顧客管理画面で顧客一覧・登録・請求書作成に利用する顧客情報');
            $table->string('id')->primary()->comment('顧客ID');
            $table->string('name')->comment('会社名（顧客一覧・請求書に表示）');
            $table->string('person')->nullable()->comment('担当者名');
            $table->string('email')->nullable()->comment('顧客連絡先メールアドレス');
            $table->string('tel')->nullable()->comment('顧客電話番号');
            $table->text('addr')->nullable()->comment('顧客住所');
            $table->string('site')->nullable()->comment('支払サイト情報');
            $table->string('reg_no')->nullable()->comment('適格請求書発行事業者登録番号');
            $table->text('memo')->nullable()->comment('顧客メモ');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
