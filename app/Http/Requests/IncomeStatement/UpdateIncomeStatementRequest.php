<?php

namespace App\Http\Requests\IncomeStatement;

use App\Services\IncomeStatementService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncomeStatementRequest extends FormRequest
{
    protected $redirectRoute = 'pl';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period_from' => ['required', 'date'],
            'period_to' => ['required', 'date', 'after_or_equal:period_from'],
            'rows' => ['nullable', 'array'],
            'rows.*.name' => ['nullable', 'string', 'max:255'],
            'rows.*.type' => ['nullable', 'string', Rule::in(IncomeStatementService::TYPES)],
            'rows.*.v' => ['nullable', 'integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'period_from' => '自',
            'period_to' => '至',
        ];
    }
}
