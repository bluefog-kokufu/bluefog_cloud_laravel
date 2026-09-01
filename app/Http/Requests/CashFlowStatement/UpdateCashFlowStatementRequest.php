<?php

namespace App\Http\Requests\CashFlowStatement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashFlowStatementRequest extends FormRequest
{
    protected $redirectRoute = 'cf';

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

        $merge = [
            'operating' => $stripCommas($this->input('operating')),
            'investing' => $stripCommas($this->input('investing')),
            'financing' => $stripCommas($this->input('financing')),
        ];

        if ($this->has('beginning_balance')) {
            $merge['beginning_balance'] = (int) str_replace(',', '', (string) $this->input('beginning_balance'));
        }

        $this->merge($merge);
    }

    public function rules(): array
    {
        return [
            'period_from' => ['required', 'date'],
            'period_to' => ['required', 'date', 'after_or_equal:period_from'],
            'beginning_balance' => ['required', 'integer'],
            'operating' => ['nullable', 'array'],
            'operating.*.name' => ['nullable', 'string', 'max:255'],
            'operating.*.v' => ['nullable', 'integer'],
            'investing' => ['nullable', 'array'],
            'investing.*.name' => ['nullable', 'string', 'max:255'],
            'investing.*.v' => ['nullable', 'integer'],
            'financing' => ['nullable', 'array'],
            'financing.*.name' => ['nullable', 'string', 'max:255'],
            'financing.*.v' => ['nullable', 'integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'period_from' => '自',
            'period_to' => '至',
            'beginning_balance' => '現金及び現金同等物の期首残高',
            'operating' => '営業活動によるキャッシュフロー',
            'operating.*.name' => '営業活動の科目',
            'operating.*.v' => '営業活動の金額',
            'investing' => '投資活動によるキャッシュフロー',
            'investing.*.name' => '投資活動の科目',
            'investing.*.v' => '投資活動の金額',
            'financing' => '財務活動によるキャッシュフロー',
            'financing.*.name' => '財務活動の科目',
            'financing.*.v' => '財務活動の金額',
        ];
    }
}
