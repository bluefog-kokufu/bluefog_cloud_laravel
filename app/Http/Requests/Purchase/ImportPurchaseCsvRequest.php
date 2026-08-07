<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class ImportPurchaseCsvRequest extends FormRequest
{
    protected $redirectRoute = 'purchase';

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
}
