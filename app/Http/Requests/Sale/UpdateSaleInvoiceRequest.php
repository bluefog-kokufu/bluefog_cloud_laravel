<?php

namespace App\Http\Requests\Sale;

use App\Services\SaleService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaleInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cust_id' => ['required', 'string', 'exists:customers,id'],
            'honorific' => ['nullable', 'string', Rule::in(SaleService::HONORIFICS)],
            'staff_name' => ['nullable', 'string', 'max:255'],
            'font_name' => ['nullable', 'integer', 'between:8,40'],
            'font_addr' => ['nullable', 'integer', 'between:8,40'],
            'font_contact' => ['nullable', 'integer', 'between:8,40'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'invoice_no' => [
                'required', 'string', 'max:255',
                Rule::unique('sales', 'invoice_no')->ignore($this->route('sale')),
            ],
            'subject' => ['nullable', 'string', 'max:255'],
            'inv_memo' => ['nullable', 'string'],
            'inv_items' => ['required', 'array', 'min:1'],
            'inv_items.*.date' => ['required', 'date'],
            'inv_items.*.item' => ['required', 'string', 'max:255'],
            'inv_items.*.price' => ['required', 'integer', 'min:1'],
            'inv_items.*.unit' => ['nullable', 'string', 'max:50'],
            'inv_items.*.qty' => ['required', 'integer', 'min:1'],
            'inv_items.*.tax' => ['required', 'string', Rule::in(SaleService::INV_TAX_OPTIONS)],
            'seal' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
            'staff_seal' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cust_id' => '取引先名',
            'honorific' => '敬称',
            'staff_name' => '担当者名',
            'invoice_date' => '請求日',
            'due_date' => '支払期日',
            'invoice_no' => '請求書番号',
            'subject' => '件名',
            'inv_items.*.date' => '明細の日付',
            'inv_items.*.item' => '明細の品目',
            'inv_items.*.price' => '明細の単価',
            'inv_items.*.qty' => '明細の数量',
            'inv_items.*.tax' => '明細の消費税',
            'seal' => '印鑑',
            'staff_seal' => '担当者印鑑',
        ];
    }
}
