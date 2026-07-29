<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLedgerEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->string('m')->nullable();
            $table->string('d')->nullable();
            $table->string('acct')->nullable();
            $table->text('note')->nullable();
            $table->string('page')->nullable();
            $table->integer('dr')->default(0);
            $table->integer('cr')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ledger_entries');
    }
}
