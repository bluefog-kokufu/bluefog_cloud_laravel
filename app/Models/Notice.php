<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 管理画面(bluefog_cloud_laravel_admin)のlaravel_admin DBのnoticesテーブルを参照する読み取り専用モデル
 */
class Notice extends Model
{
    use SoftDeletes;

    protected $connection = 'admin_mysql';

    protected $casts = [
        'published_at' => 'date',
        'is_active' => 'boolean',
    ];
}
