@php
    $items = old('items', $sale?->items->toArray() ?? [['name' => '', 'amount' => '', 'rate' => 10]]);
    $rowCount = count($items);
@endphp
<div>
    <h3>{{ $sale ? '取引編集' : '取引作成' }}(売上)・インボイス対応請求書</h3>
    @include('admin.partials.error-summary')
    <form method="POST" action="{{ $sale ? route('sale.update', $sale) : route('sale.store') }}">
        @csrf
        @if ($sale)
        @method('PUT')
        @endif
        <input type="hidden" name="sale_id" value="{{ $sale->id ?? '' }}">
        <div class="card">
            <div class="field">
                <label>取引先名<span class="req">必須</span></label>
                <select name="cust_id">
                    <option value="">選択してください</option>
                    @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('cust_id', $sale->cust_id ?? '') === $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('cust_id')<div class="field-error">{{ $message }}</div>@enderror
                <div style="margin-top:6px">
                    <a class="btn ghost small" href="{{ route('customer.create') }}" target="_blank">顧客情報を新規登録</a>
                    <span class="muted">別タブで登録後、この一覧を再読み込みしてください。</span>
                </div>
            </div>
            <div class="grid2">
                <div class="field">
                    <label>作成日<span class="req">必須</span></label>
                    <input type="date" name="date" value="{{ old('date', optional($sale?->date)->format('Y-m-d')) }}">
                    @error('date')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>入金方法<span class="req">必須</span></label>
                    <select name="method">
                        <option value="">選択してください</option>
                        @foreach (\App\Services\SaleService::METHODS as $method)
                        <option value="{{ $method }}" {{ old('method', $sale->method ?? '') === $method ? 'selected' : '' }}>{{ $method }}</option>
                        @endforeach
                    </select>
                    @error('method')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>ステータス<span class="req">必須</span></label>
                    <select name="status">
                        @foreach (\App\Services\SaleService::STATUSES as $status)
                        <option value="{{ $status }}" {{ old('status', $sale->status ?? '未請求') === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>備考(請求書には表示されません)<span class="opt">任意</span></label>
                    <input type="text" name="memo" value="{{ old('memo', $sale->memo ?? '') }}">
                </div>
            </div>
            <div class="field" style="max-width:none">
                <label>明細(品目ごとに税率を設定できます・軽減税率8%対応)</label>
                <div class="right" style="margin:0 0 8px">
                    <button type="button" class="btn ghost small" onclick="saleItemAdd()">+ 明細行を追加</button>
                </div>
                <div style="overflow-x:auto">
                    <table class="sheet" id="saleItems" data-next-index="{{ $rowCount }}">
                        <thead>
                            <tr><th>品目・内容<span class="req" style="margin-left:4px">必須</span></th><th style="width:130px">税抜金額<span class="req" style="margin-left:4px">必須</span></th><th style="width:110px">税率<span class="req" style="margin-left:4px">必須</span></th><th style="width:110px">消費税額</th><th style="width:40px"></th></tr>
                        </thead>
                        <tbody id="saleItemsBody">
                            @foreach ($items as $i => $item)
                            <tr>
                                <td><input type="text" name="items[{{ $i }}][name]" value="{{ $item['name'] ?? '' }}" placeholder="品目・内容"></td>
                                <td><input type="number" name="items[{{ $i }}][amount]" value="{{ $item['amount'] ?? '' }}" style="text-align:right" oninput="saleRowRecalc(this)"></td>
                                <td>
                                    <select name="items[{{ $i }}][rate]" onchange="saleRowRecalc(this)">
                                        <option value="10" {{ (string) ($item['rate'] ?? 10) === '10' ? 'selected' : '' }}>10%(標準)</option>
                                        <option value="8" {{ (string) ($item['rate'] ?? '') === '8' ? 'selected' : '' }}>8%(軽減)</option>
                                        <option value="0" {{ (string) ($item['rate'] ?? '') === '0' ? 'selected' : '' }}>対象外(0%)</option>
                                    </select>
                                </td>
                                <td class="num sale-item-tax" style="padding:4px 8px">¥0</td>
                                <td><button type="button" class="icon-btn" onclick="saleItemDel(this)">🗑</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @error('items')<div class="field-error">{{ $message }}</div>@enderror
                @foreach ($errors->get('items.*.name') as $messages)
                    @foreach ($messages as $message)<div class="field-error">{{ $message }}</div>@endforeach
                @endforeach
                @foreach ($errors->get('items.*.amount') as $messages)
                    @foreach ($messages as $message)<div class="field-error">{{ $message }}</div>@endforeach
                @endforeach
                <table class="sheet" style="max-width:340px;margin-left:auto;margin-top:10px">
                    <tr><td>小計(税抜)</td><td class="num" id="saleSub" style="padding:4px 8px;width:150px">¥0</td></tr>
                    <tr><td>消費税</td><td class="num" id="saleTaxTotal" style="padding:4px 8px">¥0</td></tr>
                    <tr><td class="total">請求金額(税込)</td><td class="total num" id="saleGrandTotal" style="padding:4px 8px">¥0</td></tr>
                </table>
            </div>
        </div>
        <div class="formfoot">
            <button class="btn ghost" type="button" onclick="closeModal()">キャンセル</button>
            <button class="btn" type="submit">{{ $sale ? '更新' : '作成' }}</button>
        </div>
    </form>
</div>
