<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->date('date')->nullable();
            $table->string('cust_id')->nullable()->index();
            $table->string('method')->nullable();
            $table->integer('amount')->default(0);
            $table->integer('tax')->default(0);
            $table->string('status')->default('未請求');
            $table->timestamp('invoiced')->nullable();
            $table->text('memo')->nullable();
            $table->timestamps();

            $table->foreign('cust_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
