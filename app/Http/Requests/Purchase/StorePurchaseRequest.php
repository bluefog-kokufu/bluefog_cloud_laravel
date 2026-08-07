<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    /**
     * バリデーション失敗時は取引一覧画面へリダイレクトする（fetchで開いたモーダルのURLへ戻らないようにするため）
     */
    protected $redirectRoute = 'purchase';

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
            'amount' => ['required', 'integer', 'min:0'],
            'tax' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:未払い,支払い済'],
            'memo' => ['nullable', 'string'],
            'quote' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'invoice' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'receipt' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'contract' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cust_id' => '取引先名',
            'date' => '取引年月日',
            'method' => '入金方法',
            'amount' => '取引金額',
            'tax' => '税額',
            'status' => 'ステータス',
            'quote' => '見積書',
            'invoice' => '請求書',
            'receipt' => '領収書',
            'contract' => '契約書',
        ];
    }
}
