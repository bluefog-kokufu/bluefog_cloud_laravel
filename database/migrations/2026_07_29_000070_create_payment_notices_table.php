<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentNoticesTable extends Migration
{
    public function up()
    {
        Schema::create('payment_notices', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('cust_id')->nullable()->index();
            $table->string('title');
            $table->date('pay_date')->nullable();
            $table->json('items')->nullable();
            $table->timestamps();

            $table->foreign('cust_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_notices');
    }
}
