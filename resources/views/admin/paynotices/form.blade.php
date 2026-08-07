@php
    $items = old('items', $paymentNotice?->items ?? [['date' => now()->format('Y-m-d'), 'item' => '', 'price' => '', 'unit' => '式', 'qty' => 1, 'tax' => '10%']]);
    $rowCount = count($items);
@endphp
<div>
    <h3>支払通知書{{ $paymentNotice ? '編集' : '作成' }}</h3>
    <form method="POST" action="{{ $paymentNotice ? route('paynotice.update', $paymentNotice) : route('paynotice.store') }}">
        @csrf
        @if ($paymentNotice)
        @method('PUT')
        @endif
        <input type="hidden" name="paynotice_id" value="{{ $paymentNotice->id ?? '' }}">
        <div class="card">
            <div class="secttl"><span class="n">1</span>取引先情報</div>
            <div class="field">
                <label><span class="req">必須</span>取引先名</label>
                <select name="cust_id">
                    <option value="">選択してください</option>
                    @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('cust_id', $paymentNotice->cust_id ?? '') === $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('cust_id')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="card">
            <div class="secttl"><span class="n">2</span>事業者情報</div>
            <div style="font-size:13px"><b>{{ $company->name ?? '' }}</b><br><span class="muted">〒{{ $company->zip ?? '' }} {{ $company->addr ?? '' }}</span></div>
        </div>
        <div class="card">
            <div class="secttl"><span class="n">3</span>入金口座情報</div>
            <div style="font-size:13px">{{ $company->bank ?? '' }}</div>
        </div>
        <div class="card">
            <div class="secttl"><span class="n">4</span>基本情報</div>
            <div class="grid2">
                <div class="field">
                    <label><span class="req">必須</span>支払日</label>
                    <input type="date" name="pay_date" value="{{ old('pay_date', optional($paymentNotice?->pay_date)->format('Y-m-d') ?: now()->format('Y-m-d')) }}">
                    @error('pay_date')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>支払通知書番号</label>
                    <input value="{{ $paymentNotice->id ?? '' }}" readonly style="background:#eef2f8" placeholder="保存時に自動採番されます">
                    <div class="muted">{{ $paymentNotice ? '自動発行された番号です(変更不可)' : '保存時に自動採番されます' }}</div>
                </div>
            </div>
            <div class="field">
                <label><span class="req">必須</span>件名</label>
                <input name="title" value="{{ old('title', $paymentNotice->title ?? '') }}">
                @error('title')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="card">
            <div class="secttl"><span class="n">5</span>明細情報</div>
            <div class="right"><button type="button" class="btn ghost small" onclick="paynoticeItemAdd()">明細を新規登録</button></div>
            <div style="overflow-x:auto">
                <table class="sheet" id="paynoticeItems" data-next-index="{{ $rowCount }}">
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
                    <tbody id="paynoticeItemsBody">
                        @foreach ($items as $i => $item)
                        <tr>
                            <td><input type="date" name="items[{{ $i }}][date]" value="{{ $item['date'] ?? '' }}" style="width:140px" oninput="paynoticeRowRecalc(this)"></td>
                            <td><input type="text" name="items[{{ $i }}][item]" value="{{ $item['item'] ?? '' }}" style="min-width:160px"></td>
                            <td><input type="number" name="items[{{ $i }}][price]" value="{{ $item['price'] ?? '' }}" style="width:110px;text-align:right" oninput="paynoticeRowRecalc(this)"></td>
                            <td><input type="text" name="items[{{ $i }}][unit]" value="{{ $item['unit'] ?? '式' }}" style="width:60px"></td>
                            <td><input type="number" name="items[{{ $i }}][qty]" value="{{ $item['qty'] ?? '' }}" style="width:70px;text-align:right" oninput="paynoticeRowRecalc(this)"></td>
                            <td>
                                <select name="items[{{ $i }}][tax]" style="width:110px" onchange="paynoticeRowRecalc(this)">
                                    @foreach (\App\Services\PaymentNoticeService::TAX_OPTIONS as $taxOption)
                                    <option value="{{ $taxOption }}" {{ ($item['tax'] ?? '10%') === $taxOption ? 'selected' : '' }}>{{ $taxOption }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="num paynotice-item-amount" style="padding:4px 8px">0</td>
                            <td><button type="button" class="icon-btn" onclick="paynoticeItemDel(this)">🗑</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @error('items')<div class="field-error">{{ $message }}</div>@enderror
            @foreach ($errors->get('items.*.date') as $messages)
                @foreach ($messages as $message)<div class="field-error">{{ $message }}</div>@endforeach
            @endforeach
            @foreach ($errors->get('items.*.item') as $messages)
                @foreach ($messages as $message)<div class="field-error">{{ $message }}</div>@endforeach
            @endforeach
            @foreach ($errors->get('items.*.price') as $messages)
                @foreach ($messages as $message)<div class="field-error">{{ $message }}</div>@endforeach
            @endforeach
            @foreach ($errors->get('items.*.qty') as $messages)
                @foreach ($messages as $message)<div class="field-error">{{ $message }}</div>@endforeach
            @endforeach
            <table class="sheet" style="max-width:360px;margin-left:auto;margin-top:12px">
                <tr><td>小計</td><td class="num" id="pnSub" style="padding:4px 8px;width:160px">¥0</td></tr>
                <tr><td>消費税</td><td class="num" id="pnTax" style="padding:4px 8px">¥0</td></tr>
                <tr><td class="total">合計(税込み)</td><td class="total num" id="pnTotal" style="padding:4px 8px">¥0</td></tr>
            </table>
        </div>
        <div class="formfoot">
            <button class="btn ghost" type="button" onclick="closeModal()">キャンセル</button>
            <button class="btn" type="submit">{{ $paymentNotice ? '更新' : '作成' }}</button>
        </div>
    </form>
</div>
