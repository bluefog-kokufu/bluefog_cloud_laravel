<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentNoticesTable extends Migration
{
    public function up()
    {
        Schema::create('payment_notices', function (Blueprint $table) {
            $table->comment('支払通知書一覧・作成画面で利用する支払通知書');
            $table->string('id')->primary()->comment('支払通知書ID');
            $table->string('cust_id')->nullable()->index()->comment('顧客ID');
            $table->string('title')->comment('通知書タイトル');
            $table->date('pay_date')->nullable()->comment('支払期日');
            $table->json('items')->nullable()->comment('通知書明細');
            $table->timestamps();

            $table->foreign('cust_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_notices');
    }
}
