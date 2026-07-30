<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->comment('発注取引一覧(アップロード)・書類アップロード画面で利用する仕入取引');
            $table->string('id')->primary()->comment('仕入取引ID');
            $table->date('date')->nullable()->comment('取引年月日');
            $table->string('cust_id')->nullable()->index()->comment('顧客ID');
            $table->string('method')->nullable()->comment('支払方法');
            $table->integer('amount')->default(0)->comment('税抜金額');
            $table->integer('tax')->default(0)->comment('税額');
            $table->string('status')->default('未払い')->comment('支払ステータス');
            $table->json('files')->nullable()->comment('アップロード済み書類情報');
            $table->text('memo')->nullable()->comment('メモ');
            $table->date('up')->nullable()->comment('アップロード日');
            $table->timestamps();

            $table->foreign('cust_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
