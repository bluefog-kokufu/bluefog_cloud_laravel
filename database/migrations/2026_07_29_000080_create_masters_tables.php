<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMastersTables extends Migration
{
    public function up()
    {
        Schema::create('m_landlords', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->text('addr')->nullable();
            $table->text('memo')->nullable();
            $table->timestamps();
        });

        Schema::create('m_contractors', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->text('addr')->nullable();
            $table->text('memo')->nullable();
            $table->timestamps();
        });

        Schema::create('m_repairers', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->text('addr')->nullable();
            $table->text('memo')->nullable();
            $table->timestamps();
        });

        Schema::create('m_agents', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->text('addr')->nullable();
            $table->text('memo')->nullable();
            $table->timestamps();
        });

        Schema::create('m_insurers', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->text('addr')->nullable();
            $table->text('memo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_insurers');
        Schema::dropIfExists('m_agents');
        Schema::dropIfExists('m_repairers');
        Schema::dropIfExists('m_contractors');
        Schema::dropIfExists('m_landlords');
    }
}
