<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeStatement extends Model
{
    protected $table = 'income_statements';

    protected $fillable = [
        'period_from',
        'period_to',
        'rows',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
        'rows' => 'array',
    ];
}
