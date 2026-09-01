<?php

namespace App\Http\Requests\Ledger;

use Illuminate\Foundation\Http\FormRequest;

class ImportLedgerCsvRequest extends FormRequest
{
    protected $redirectRoute = 'ledger';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ];
    }

    public function attributes(): array
    {
        return [
            'csv_file' => 'CSVファイル',
        ];
    }
}
