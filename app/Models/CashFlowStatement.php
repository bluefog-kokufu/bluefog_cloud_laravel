<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlowStatement extends Model
{
    protected $table = 'cash_flow_statements';

    protected $fillable = [
        'period_from',
        'period_to',
        'beginning_balance',
        'operating',
        'investing',
        'financing',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
        'beginning_balance' => 'integer',
        'operating' => 'array',
        'investing' => 'array',
        'financing' => 'array',
    ];
}
