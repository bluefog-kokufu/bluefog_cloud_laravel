<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $table = 'ledger_entries';

    protected $fillable = [
        'no',
        'year',
        'm',
        'd',
        'dr_acct',
        'dr_amt',
        'cr_acct',
        'cr_amt',
        'note',
        'page',
    ];

    protected $casts = [
        'dr_amt' => 'integer',
        'cr_amt' => 'integer',
    ];
}
