<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->comment('受注取引一覧・取引作成・請求書作成画面で利用する売上取引');
            $table->string('id')->primary()->comment('売上取引ID');
            $table->date('date')->nullable()->comment('取引年月日');
            $table->string('cust_id')->nullable()->index()->comment('顧客ID');
            $table->string('method')->nullable()->comment('入金方法');
            $table->integer('amount')->default(0)->comment('税抜金額');
            $table->integer('tax')->default(0)->comment('税額');
            $table->string('status')->default('未請求')->comment('請求・入金ステータス');
            $table->timestamp('invoiced')->nullable()->comment('請求書発行日時');
            $table->text('memo')->nullable()->comment('備考');
            $table->timestamps();

            $table->foreign('cust_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
