<?php

namespace App\Http\Requests\PaymentNotice;

use App\Services\PaymentNoticeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'max:255', 'unique:payment_notices,id'],
            'cust_id' => ['required', 'string', 'exists:customers,id'],
            'pay_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.date' => ['required', 'date'],
            'items.*.item' => ['required', 'string', 'max:255'],
            'items.*.price' => ['required', 'integer', 'min:1'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.tax' => ['required', 'string', Rule::in(PaymentNoticeService::TAX_OPTIONS)],
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => '支払通知書番号',
            'cust_id' => '取引先名',
            'pay_date' => '支払日',
            'title' => '件名',
            'items' => '明細',
            'items.*.date' => '明細の日付',
            'items.*.item' => '明細の品目',
            'items.*.price' => '明細の単価',
            'items.*.qty' => '明細の数量',
            'items.*.tax' => '明細の消費税',
        ];
    }
}
