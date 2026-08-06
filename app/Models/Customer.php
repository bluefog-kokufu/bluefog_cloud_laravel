<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    protected $table = 'customers';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'type',
        'zip',
        'pref',
        'addr1',
        'addr2',
        'person',
        'email',
        'tel',
        'mobile',
        'fax',
        'url',
        'addr',
        'site',
        'reg_no',
        'memo',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = 'c' . Str::random(8);
            }
        });
    }
}
