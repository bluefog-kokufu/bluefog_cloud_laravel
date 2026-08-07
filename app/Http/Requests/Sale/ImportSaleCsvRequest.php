<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class ImportSaleCsvRequest extends FormRequest
{
    protected $redirectRoute = 'sale';

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
