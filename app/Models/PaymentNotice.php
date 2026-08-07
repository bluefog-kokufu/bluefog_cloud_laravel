<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentNotice extends Model
{
    protected $table = 'payment_notices';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'cust_id',
        'title',
        'pay_date',
        'items',
    ];

    protected $casts = [
        'pay_date' => 'date',
        'items' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }
}
