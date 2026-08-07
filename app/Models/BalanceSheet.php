<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceSheet extends Model
{
    protected $table = 'balance_sheets';

    protected $fillable = [
        'date',
        'assets',
        'liabs',
        'equity',
    ];

    protected $casts = [
        'date' => 'date',
        'assets' => 'array',
        'liabs' => 'array',
        'equity' => 'array',
    ];
}
