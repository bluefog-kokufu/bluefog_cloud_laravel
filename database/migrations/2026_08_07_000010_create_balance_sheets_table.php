<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalanceSheetsTable extends Migration
{
    public function up()
    {
        Schema::create('balance_sheets', function (Blueprint $table) {
            $table->comment('貸借対照表画面で利用する貸借対照表（会社ごとに1件）');
            $table->id()->comment('貸借対照表ID');
            $table->date('date')->comment('基準日');
            $table->json('assets')->nullable()->comment('資産の部の明細');
            $table->json('liabs')->nullable()->comment('負債の部の明細');
            $table->json('equity')->nullable()->comment('純資産の部の明細');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('balance_sheets');
    }
}
