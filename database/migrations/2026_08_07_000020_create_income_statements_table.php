<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeStatementsTable extends Migration
{
    public function up()
    {
        Schema::create('income_statements', function (Blueprint $table) {
            $table->comment('損益計算書画面で利用する損益計算書（会社ごとに1件）');
            $table->id()->comment('損益計算書ID');
            $table->date('period_from')->comment('対象期間の開始日');
            $table->date('period_to')->comment('対象期間の終了日');
            $table->json('rows')->nullable()->comment('科目明細（科目名・区分・金額）');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('income_statements');
    }
}
