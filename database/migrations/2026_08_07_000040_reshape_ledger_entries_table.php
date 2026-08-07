<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReshapeLedgerEntriesTable extends Migration
{
    public function up()
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropColumn(['acct', 'dr', 'cr']);
            $table->string('year')->nullable()->after('no')->comment('年');
            $table->string('dr_acct')->nullable()->after('d')->comment('借方勘定科目');
            $table->integer('dr_amt')->default(0)->after('dr_acct')->comment('借方金額');
            $table->string('cr_acct')->nullable()->after('dr_amt')->comment('貸方勘定科目');
            $table->integer('cr_amt')->default(0)->after('cr_acct')->comment('貸方金額');
        });
    }

    public function down()
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropColumn(['year', 'dr_acct', 'dr_amt', 'cr_acct', 'cr_amt']);
            $table->string('acct')->nullable()->comment('勘定科目');
            $table->integer('dr')->default(0)->comment('借方金額');
            $table->integer('cr')->default(0)->comment('貸方金額');
        });
    }
}
