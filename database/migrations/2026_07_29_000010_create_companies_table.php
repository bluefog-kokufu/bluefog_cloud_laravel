<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->comment('会計・消費税設定画面で利用する会社情報');
            $table->id()->comment('会社ID');
            $table->string('name')->comment('会社名（設定画面・請求書に表示）');
            $table->string('reg_no')->nullable()->comment('適格請求書発行事業者登録番号');
            $table->string('zip')->nullable()->comment('会社郵便番号');
            $table->text('addr')->nullable()->comment('会社住所');
            $table->string('tel')->nullable()->comment('会社電話番号');
            $table->text('bank')->nullable()->comment('振込先銀行口座情報');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
