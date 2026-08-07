<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashFlowStatementsTable extends Migration
{
    public function up()
    {
        Schema::create('cash_flow_statements', function (Blueprint $table) {
            $table->comment('キャッシュフロー計算書画面で利用するキャッシュフロー計算書（会社ごとに1件）');
            $table->id()->comment('キャッシュフロー計算書ID');
            $table->date('period_from')->comment('対象期間の開始日');
            $table->date('period_to')->comment('対象期間の終了日');
            $table->integer('beginning_balance')->default(0)->comment('現金及び現金同等物の期首残高');
            $table->json('operating')->nullable()->comment('営業活動によるキャッシュ・フローの明細');
            $table->json('investing')->nullable()->comment('投資活動によるキャッシュ・フローの明細');
            $table->json('financing')->nullable()->comment('財務活動によるキャッシュ・フローの明細');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_flow_statements');
    }
}
