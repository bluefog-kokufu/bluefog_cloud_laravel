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

    /**
     * 金額入力欄はカンマ区切りで表示しているため、バリデーション前にカンマを取り除く
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'rows' => collect($this->input('rows') ?? [])->map(function ($row) {
                foreach (['dr_amt', 'cr_amt'] as $key) {
                    if (isset($row[$key]) && $row[$key] !== '') {
                        $row[$key] = (int) str_replace(',', '', (string) $row[$key]);
                    }
                }

                return $row;
            })->all(),
        ]);
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

    public function attributes(): array
    {
        return [
            'rows' => '仕訳',
            'rows.*.no' => '伝票No.',
            'rows.*.year' => '年',
            'rows.*.m' => '月',
            'rows.*.d' => '日',
            'rows.*.dr_acct' => '借方勘定科目',
            'rows.*.dr_amt' => '借方金額',
            'rows.*.cr_acct' => '貸方勘定科目',
            'rows.*.cr_amt' => '貸方金額',
            'rows.*.note' => '摘要',
            'rows.*.page' => '仕丁',
        ];
    }
}
