<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sale extends Model
{
    protected $table = 'sales';

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
        'invoiced',
        'memo',
        'files',
    ];

    protected $casts = [
        'date' => 'date',
        'invoiced' => 'datetime',
        'amount' => 'integer',
        'tax' => 'integer',
        'files' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::ulid()->toBase32();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }
}
