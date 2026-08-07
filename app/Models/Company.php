<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'tax_rate',
        'rounding',
        'reg_no',
        'zip',
        'addr',
        'tel',
        'bank',
    ];

    protected $casts = [
        'tax_rate' => 'integer',
    ];
}
