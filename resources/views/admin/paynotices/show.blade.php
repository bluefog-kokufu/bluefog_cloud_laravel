@php
    $items = $paymentNotice->items ?? [];
@endphp
<div>
    <div id="paynoticeContent">
        <div class="invoice">
            <h1>支払通知書</h1>
            <div class="inv-head">
                <div>
                    <div style="font-size:15px;font-weight:700;border-bottom:1px solid #333;padding-bottom:4px;margin-bottom:6px">{{ $customer->name ?? '(削除済み)' }} 御中</div>
                    <div class="muted">{{ $customer->pref ?? '' }}{{ $customer->addr1 ?? '' }}{{ $customer->addr2 ?? '' }}</div>
                    <div style="margin-top:14px">下記のとおりお支払いいたします。</div>
                    <div class="inv-total">お支払金額(税込) ¥{{ number_format($totals['total']) }}</div>
                    <div style="margin-top:8px;font-size:13px">お支払日:{{ optional($paymentNotice->pay_date)->format('Y.m.d') }}</div>
                </div>
                <div style="text-align:right;font-size:12px">
                    <div>通知番号:{{ $paymentNotice->id }}</div>
                    <div>作成日:{{ optional($paymentNotice->created_at)->format('Y.m.d') }}</div>
                    <div style="margin-top:10px;font-weight:700;font-size:14px">{{ $company->name ?? '' }}</div>
                    <div>〒{{ $company->zip ?? '' }} {{ $company->addr ?? '' }}</div>
                    <div>TEL:{{ $company->tel ?? '' }}</div>
                    <div>登録番号:{{ $company->reg_no ?? '' }}</div>
                </div>
            </div>
            @if ($paymentNotice->title)
            <div style="font-size:13px;margin-bottom:6px">件名:{{ $paymentNotice->title }}</div>
            @endif
            <table class="inv">
                <tr><th>日付</th><th style="width:40%">品目</th><th class="num">単価</th><th>単位</th><th class="num">数量</th><th>消費税</th><th class="num">金額</th></tr>
                @foreach ($items as $item)
                @php $itemAmount = (int) ($item['price'] ?? 0) * (int) ($item['qty'] ?? 0); @endphp
                <tr>
                    <td>{{ isset($item['date']) ? str_replace('-', '.', $item['date']) : '' }}</td>
                    <td>{{ $item['item'] ?? '' }}</td>
                    <td class="num">{{ number_format($item['price'] ?? 0) }}</td>
                    <td style="text-align:center">{{ $item['unit'] ?? '' }}</td>
                    <td class="num">{{ number_format($item['qty'] ?? 0) }}</td>
                    <td style="text-align:center">{{ $item['tax'] ?? '' }}</td>
                    <td class="num">{{ number_format($itemAmount) }}</td>
                </tr>
                @endforeach
                <tr><td colspan="6" style="text-align:right">小計</td><td class="num">¥{{ number_format($totals['sub']) }}</td></tr>
                <tr><td colspan="6" style="text-align:right">消費税</td><td class="num">¥{{ number_format($totals['tax']) }}</td></tr>
                <tr><td colspan="6" style="text-align:right;font-weight:700">合計(税込み)</td><td class="num" style="font-weight:700">¥{{ number_format($totals['total']) }}</td></tr>
            </table>
            <div style="margin-top:16px;font-size:12px"><b>お振込先(入金口座)</b>:{{ $company->bank ?? '' }}</div>
        </div>
    </div>
    <div class="formfoot no-print" style="justify-content:center">
        <button class="btn ghost" type="button" onclick="closeModal()">閉じる</button>
        <button class="btn accent" type="button" onclick="paynoticePrint()">印刷 / PDF保存</button>
    </div>
</div>
