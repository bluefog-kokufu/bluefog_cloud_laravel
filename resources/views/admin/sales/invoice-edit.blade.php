@extends('layouts.app')

@php
    $items = old('inv_items', $invItems);
    $rowCount = count($items);
@endphp

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / <a href="{{ route('sale') }}">受注取引一覧</a> / 請求書作成</div>
<h2 class="pagettl">請求書作成</h2>
<div class="panel">
    <form id="saleInvoiceForm" method="POST" action="{{ route('sale.invoice.update', $sale) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="secttl"><span class="n">1</span>取引先情報</div>
            <div class="field">
                <label><span class="req">必須</span>取引先名</label>
                <select name="cust_id">
                    <option value="">選択してください</option>
                    @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('cust_id', $sale->cust_id) === $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('cust_id')<div class="field-error">{{ $message }}</div>@enderror
                <div style="margin-top:6px">
                    <button type="button" class="btn ghost small" onclick="customerQuickCreate()">顧客情報を新規登録</button>
                </div>
            </div>
            <div class="field">
                <label>敬称</label>
                <select name="honorific">
                    @foreach (\App\Services\SaleService::HONORIFICS as $honorific)
                    <option value="{{ $honorific }}" {{ old('honorific', $sale->honorific ?? '様') === $honorific ? 'selected' : '' }}>{{ $honorific }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card">
            <div class="secttl"><span class="n">2</span>事業者情報 <a class="btn ghost small" href="{{ route('settings') }}" target="_blank">事業者(自社)情報の編集</a></div>
            <div class="grid2">
                <div style="font-size:13px">
                    <b>{{ $company->name ?? '' }}</b><br>
                    <span class="muted">〒{{ $company->zip ?? '' }} {{ $company->addr ?? '' }}</span><br>
                    <span class="muted">登録番号: {{ $company->reg_no ?? '' }}</span>
                </div>
                <div class="field">
                    <label>担当者名</label>
                    <input type="text" name="staff_name" value="{{ old('staff_name', $sale->staff_name ?? '') }}">
                    @error('staff_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="muted" style="font-size:12px;margin-top:8px">請求書PDFの送信者情報に表示する文字サイズを調整します。単位はptです。</div>
            <div class="grid2" style="margin-top:6px">
                <div class="field">
                    <label>送信者名サイズ(pt)</label>
                    <input type="number" name="font_name" min="8" max="40" value="{{ old('font_name', $sale->font_name ?? 14) }}">
                    @error('font_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>送信者住所サイズ(pt)</label>
                    <input type="number" name="font_addr" min="8" max="40" value="{{ old('font_addr', $sale->font_addr ?? 12) }}">
                    @error('font_addr')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>送信者連絡先サイズ(pt)</label>
                    <input type="number" name="font_contact" min="8" max="40" value="{{ old('font_contact', $sale->font_contact ?? 12) }}">
                    @error('font_contact')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="card">
            <div class="secttl"><span class="n">3</span>入金口座情報 <button type="button" class="btn ghost small" onclick="companyBankEdit()">編集 預金口座情報</button></div>
            <div class="companyBankText" style="font-size:13px">{{ $company->bank ?? '' }}</div>
        </div>
        <div class="card">
            <div class="secttl"><span class="n">4</span>基本情報</div>
            <div class="grid2">
                <div class="field">
                    <label><span class="req">必須</span>請求日</label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', optional($sale->invoice_date)->format('Y-m-d') ?: now()->format('Y-m-d')) }}">
                    @error('invoice_date')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label><span class="req">必須</span>支払期日</label>
                    <input type="date" name="due_date" value="{{ old('due_date', optional($sale->due_date)->format('Y-m-d') ?: now()->addDays(30)->format('Y-m-d')) }}">
                    @error('due_date')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="grid2">
                <div class="field">
                    <label><span class="req">必須</span>請求書番号</label>
                    <input type="text" name="invoice_no" id="inv_no" value="{{ old('invoice_no', $sale->invoice_no ?: $invoiceNo) }}" readonly style="background:#eef2f8">
                    @if ($sale->invoice_no)
                    <div class="muted">確定済みの番号です(変更不可)</div>
                    @else
                    <a onclick="saleInvoiceRegenNo()" style="font-size:12px;cursor:pointer">🔄 自動番号発行</a>
                    @endif
                    @error('invoice_no')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>件名</label>
                    <input type="text" name="subject" value="{{ old('subject', $sale->subject ?? '') }}">
                    @error('subject')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="grid2">
                @foreach (\App\Services\SaleService::SEALS as $key => $label)
                @php $existing = $sale->files[$key] ?? null; @endphp
                <div class="field">
                    <label>{{ $label }}</label>
                    <div class="upload-box {{ $existing ? 'has' : '' }}" id="ub_{{ $key }}"
                         ondragover="event.preventDefault();this.classList.add('over')"
                         ondragleave="this.classList.remove('over')"
                         ondrop="saleSealDrop(event,'{{ $key }}')"
                         onclick="document.getElementById('uf_{{ $key }}').click()" style="cursor:pointer">
                        @if ($existing)
                        ✓ アップロード済み: {{ $existing['name'] }}
                        @else
                        ⬆ {{ $label }}画像をここにドロップ<br>PNG, JPEG (2MB以下)
                        @endif
                    </div>
                    <input type="file" name="{{ $key }}" id="uf_{{ $key }}" accept=".png,.jpg,.jpeg" style="display:none" onchange="saleSealPicked('{{ $key }}',this.files[0])">
                    @error($key)<div class="field-error">{{ $message }}</div>@enderror
                </div>
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="secttl"><span class="n">5</span>明細情報</div>
            <div class="right"><button type="button" class="btn ghost small" onclick="saleInvItemAdd()">請求情報を新規登録</button></div>
            <div style="overflow-x:auto">
                <table class="sheet" id="saleInvItems" data-next-index="{{ $rowCount }}">
                    <thead>
                        <tr>
                            <th>日付<span class="req" style="margin-left:4px">必須</span></th>
                            <th>品目<span class="req" style="margin-left:4px">必須</span></th>
                            <th>単価<span class="req" style="margin-left:4px">必須</span></th>
                            <th>単位</th>
                            <th>数量<span class="req" style="margin-left:4px">必須</span></th>
                            <th>消費税</th>
                            <th>金額</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="saleInvItemsBody">
                        @foreach ($items as $i => $item)
                        <tr>
                            <td><input type="date" name="inv_items[{{ $i }}][date]" value="{{ $item['date'] ?? '' }}" style="width:140px" oninput="saleInvRowRecalc(this)"></td>
                            <td><input type="text" name="inv_items[{{ $i }}][item]" value="{{ $item['item'] ?? '' }}" style="min-width:160px"></td>
                            <td><input type="number" name="inv_items[{{ $i }}][price]" value="{{ $item['price'] ?? '' }}" style="width:110px;text-align:right" oninput="saleInvRowRecalc(this)"></td>
                            <td><input type="text" name="inv_items[{{ $i }}][unit]" value="{{ $item['unit'] ?? '式' }}" style="width:60px"></td>
                            <td><input type="number" name="inv_items[{{ $i }}][qty]" value="{{ $item['qty'] ?? 1 }}" style="width:70px;text-align:right" oninput="saleInvRowRecalc(this)"></td>
                            <td>
                                <select name="inv_items[{{ $i }}][tax]" style="width:110px" onchange="saleInvRowRecalc(this)">
                                    @foreach (\App\Services\SaleService::INV_TAX_OPTIONS as $taxOption)
                                    <option value="{{ $taxOption }}" {{ ($item['tax'] ?? '10%') === $taxOption ? 'selected' : '' }}>{{ $taxOption }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="num sale-inv-item-amount" style="padding:4px 8px">0</td>
                            <td><button type="button" class="icon-btn" onclick="saleInvItemDel(this)">🗑</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @error('inv_items')<div class="field-error">{{ $message }}</div>@enderror
            @foreach ($errors->get('inv_items.*.date') as $messages)
                @foreach ($messages as $message)<div class="field-error">{{ $message }}</div>@endforeach
            @endforeach
            @foreach ($errors->get('inv_items.*.item') as $messages)
                @foreach ($messages as $message)<div class="field-error">{{ $message }}</div>@endforeach
            @endforeach
            @foreach ($errors->get('inv_items.*.price') as $messages)
                @foreach ($messages as $message)<div class="field-error">{{ $message }}</div>@endforeach
            @endforeach
            @foreach ($errors->get('inv_items.*.qty') as $messages)
                @foreach ($messages as $message)<div class="field-error">{{ $message }}</div>@endforeach
            @endforeach
            <table class="sheet" style="max-width:360px;margin-left:auto;margin-top:12px">
                <tr><td>小計</td><td class="num" id="saleInvSub" style="padding:4px 8px;width:160px">¥0</td></tr>
                <tr><td>消費税</td><td class="num" id="saleInvTax" style="padding:4px 8px">¥0</td></tr>
                <tr><td class="total">合計(税込み)</td><td class="total num" id="saleInvTotal" style="padding:4px 8px">¥0</td></tr>
            </table>
        </div>
        <div class="card">
            <div class="secttl"><span class="n">6</span>メモ</div>
            <div class="field" style="max-width:none">
                <textarea name="inv_memo" rows="4" style="width:100%">{{ old('inv_memo', $sale->inv_memo ?? '') }}</textarea>
                @error('inv_memo')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="formfoot">
            <a class="btn ghost" href="{{ route('sale') }}">キャンセル</a>
            <button class="btn ghost" type="button" onclick="saleInvoicePreview('{{ $sale->id }}')">プレビュー</button>
            <button class="btn" type="submit">作成</button>
        </div>
    </form>
</div>
@endsection
