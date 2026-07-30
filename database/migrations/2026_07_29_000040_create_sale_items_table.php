<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleItemsTable extends Migration
{
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->comment('受注取引・請求書作成画面の売上明細行');
            $table->id()->comment('明細ID');
            $table->string('sale_id')->index()->comment('売上取引ID');
            $table->string('name')->comment('品目・内容');
            $table->integer('amount')->default(0)->comment('税抜金額');
            $table->integer('rate')->default(10)->comment('消費税率');
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_items');
    }
}
