<?php

namespace App\Http\Requests\Ledger;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLedgerRequest extends FormRequest
{
    protected $redirectRoute = 'ledger';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rows' => ['nullable', 'array'],
            'rows.*.no' => ['nullable', 'string', 'max:50'],
            'rows.*.year' => ['nullable', 'string', 'max:4'],
            'rows.*.m' => ['nullable', 'string', 'max:2'],
            'rows.*.d' => ['nullable', 'string', 'max:2'],
            'rows.*.dr_acct' => ['nullable', 'string', 'max:255'],
            'rows.*.dr_amt' => ['nullable', 'integer'],
            'rows.*.cr_acct' => ['nullable', 'string', 'max:255'],
            'rows.*.cr_amt' => ['nullable', 'integer'],
            'rows.*.note' => ['nullable', 'string', 'max:255'],
            'rows.*.page' => ['nullable', 'string', 'max:20'],
        ];
    }
}
