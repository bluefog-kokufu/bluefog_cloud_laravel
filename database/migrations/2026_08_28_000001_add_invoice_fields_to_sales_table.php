<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('honorific', 10)->nullable()->after('files')->comment('敬称(御中/様/(なし))');
            $table->string('staff_name')->nullable()->after('honorific')->comment('担当者名');
            $table->unsignedSmallInteger('font_name')->nullable()->after('staff_name')->comment('送信者名のフォントサイズ(pt)');
            $table->unsignedSmallInteger('font_addr')->nullable()->after('font_name')->comment('住所のフォントサイズ(pt)');
            $table->unsignedSmallInteger('font_contact')->nullable()->after('font_addr')->comment('連絡先のフォントサイズ(pt)');
            $table->date('invoice_date')->nullable()->after('font_contact')->comment('請求日');
            $table->date('due_date')->nullable()->after('invoice_date')->comment('支払期日');
            $table->string('invoice_no')->nullable()->unique()->after('due_date')->comment('請求書番号');
            $table->string('subject')->nullable()->after('invoice_no')->comment('件名');
            $table->text('inv_memo')->nullable()->after('subject')->comment('請求書メモ');
            $table->json('inv_items')->nullable()->after('inv_memo')->comment('請求書明細(取引明細とは独立)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'honorific',
                'staff_name',
                'font_name',
                'font_addr',
                'font_contact',
                'invoice_date',
                'due_date',
                'invoice_no',
                'subject',
                'inv_memo',
                'inv_items',
            ]);
        });
    }
};
