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

    /**
     * 金額入力欄はカンマ区切りで表示しているため、バリデーション前にカンマを取り除く
     */
    protected function prepareForValidation(): void
    {
        $stripCommas = fn (?array $rows) => collect($rows ?? [])->map(function ($row) {
            if (isset($row['v'])) {
                $row['v'] = (int) str_replace(',', '', (string) $row['v']);
            }

            return $row;
        })->all();

        $this->merge([
            'assets' => $stripCommas($this->input('assets')),
            'liabs' => $stripCommas($this->input('liabs')),
            'equity' => $stripCommas($this->input('equity')),
        ]);
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
