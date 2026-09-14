<?php

namespace App\Models;

use App\Support\ZipCode;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * 郵便番号はハイフンなしで保存されていても、表示時は常にハイフン付きにする
     */
    protected function zip(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => ZipCode::format($value),
        );
    }
}
