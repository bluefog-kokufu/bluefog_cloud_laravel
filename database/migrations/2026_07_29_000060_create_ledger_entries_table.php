<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLedgerEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->comment('総勘定元帳画面で利用する仕訳データ');
            $table->id()->comment('仕訳ID');
            $table->string('no')->nullable()->comment('伝票No.');
            $table->string('m')->nullable()->comment('月');
            $table->string('d')->nullable()->comment('日');
            $table->string('acct')->nullable()->comment('勘定科目');
            $table->text('note')->nullable()->comment('摘要');
            $table->string('page')->nullable()->comment('仕丁');
            $table->integer('dr')->default(0)->comment('借方金額');
            $table->integer('cr')->default(0)->comment('貸方金額');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ledger_entries');
    }
}
