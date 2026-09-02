<?php

namespace App\Http\Requests\Settings;

use App\Services\SettingsService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    protected $redirectRoute = 'settings';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tax_rate' => ['required', 'integer', Rule::in(SettingsService::TAX_RATES)],
            'rounding' => ['required', 'string', Rule::in(array_keys(SettingsService::ROUNDING_OPTIONS))],
            'name' => ['required', 'string', 'max:255'],
            'reg_no' => ['required', 'string', 'max:50'],
            'zip' => ['required', 'string', 'max:20'],
            'tel' => ['required', 'string', 'max:50'],
            'addr' => ['required', 'string', 'max:255'],
            'bank' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tax_rate' => '消費税率',
            'rounding' => '端数処理',
            'name' => '会社名',
            'reg_no' => '適格請求書発行事業者 登録番号',
            'zip' => '郵便番号',
            'tel' => '電話番号',
            'addr' => '住所',
            'bank' => '振込先',
        ];
    }
}
