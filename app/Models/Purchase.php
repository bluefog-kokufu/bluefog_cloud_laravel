<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Purchase extends Model
{
    protected $table = 'purchases';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'date',
        'cust_id',
        'method',
        'amount',
        'tax',
        'status',
        'files',
        'memo',
        'up',
    ];

    protected $casts = [
        'date' => 'date',
        'up' => 'date',
        'amount' => 'integer',
        'tax' => 'integer',
        'files' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::lower(Str::random(9));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }
}
