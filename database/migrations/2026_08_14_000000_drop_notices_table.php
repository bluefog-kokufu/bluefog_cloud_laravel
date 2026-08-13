<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * お知らせは管理画面(bluefog_cloud_laravel_admin)のlaravel_admin DBで一元管理するため、frontのnoticesテーブルは廃止する
     */
    public function up(): void
    {
        Schema::dropIfExists('notices');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->date('published_at');
            $table->string('title');
            $table->text('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
