<?php

namespace App\Http\Requests\BalanceSheet;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBalanceSheetRequest extends FormRequest
{
    protected $redirectRoute = 'bs';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'assets' => ['nullable', 'array'],
            'assets.*.name' => ['nullable', 'string', 'max:255'],
            'assets.*.v' => ['nullable', 'integer'],
            'liabs' => ['nullable', 'array'],
            'liabs.*.name' => ['nullable', 'string', 'max:255'],
            'liabs.*.v' => ['nullable', 'integer'],
            'equity' => ['nullable', 'array'],
            'equity.*.name' => ['nullable', 'string', 'max:255'],
            'equity.*.v' => ['nullable', 'integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'date' => '日付',
        ];
    }
}
