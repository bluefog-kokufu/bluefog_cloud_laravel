<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('person')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->text('addr')->nullable();
            $table->string('site')->nullable();
            $table->string('reg_no')->nullable();
            $table->text('memo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
