<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    /**
     * バリデーション失敗時は取引一覧画面へリダイレクトする（fetchで開いたモーダルのURLへ戻らないようにするため）
     */
    protected $redirectRoute = 'sale';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cust_id' => ['required', 'string', 'exists:customers,id'],
            'date' => ['required', 'date'],
            'method' => ['required', 'string', 'in:現金,普通預金,当座預金,クレジット'],
            'status' => ['required', 'string', 'in:未請求,請求済,入金済,回収不能'],
            'memo' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.amount' => ['required', 'integer', 'min:1'],
            'items.*.rate' => ['required', 'integer', 'in:10,8,0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cust_id' => '取引先名',
            'date' => '作成日',
            'method' => '入金方法',
            'status' => 'ステータス',
            'items.*.name' => '品目・内容',
            'items.*.amount' => '税抜金額',
            'items.*.rate' => '税率',
        ];
    }
}
