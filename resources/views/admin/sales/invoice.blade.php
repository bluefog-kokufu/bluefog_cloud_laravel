@if (isset($error))
<div>
    <div class="field-error">{{ $error }}</div>
    <div class="formfoot no-print" style="justify-content:center">
        <button class="btn ghost" type="button" onclick="closeModal()">閉じる</button>
    </div>
</div>
@else
@php
    $rateLabels = [10 => '10%対象', 8 => '軽減8%対象', 0 => '対象外(非課税)'];
    $rows = $totals['groups'] ?? [];
    $multiPerRate = collect($items)->groupBy(fn ($item) => (int) (is_array($item) ? $item['rate'] : $item->rate))
        ->contains(fn ($group) => $group->count() > 1);
    $honorific = $sale->honorific ?: '様';
    $invoiceNo = $sale->invoice_no ?: $sale->id;
    $invoiceDate = $sale->invoice_date ?? $sale->date;
@endphp
<div id="invoiceContent">
    <div class="invoice">
        @if ($sale->files['seal'] ?? null)
        <img class="inv-seal" src="{{ $sale->files['seal']['dataUrl'] ?? route('sale.seal', [$sale, 'seal']) }}" alt="印鑑">
        @endif
        <h1>請 求 書</h1>
        <div class="inv-head">
            <div>
                <div style="font-size:15px;font-weight:700;border-bottom:1px solid #333;padding-bottom:4px;margin-bottom:6px">{{ $customer->name ?? '(削除済み)' }}{{ $honorific !== '(なし)' ? ' '.$honorific : '' }}</div>
                @if ($sale->subject)
                <div style="margin-top:4px">件名: {{ $sale->subject }}</div>
                @endif
                <div style="margin-top:6px">下記の通りご請求申し上げます。</div>
            </div>
            <div style="text-align:right;font-size:12px">
                <div>No. {{ $invoiceNo }}</div>
                <div>発行日: {{ optional($invoiceDate)->format('Y-m-d') }}</div>
            </div>
        </div>
        <div class="inv-head" style="margin-top:2px">
            <div class="muted" style="font-size:12px">{{ $customer->pref ?? '' }}{{ $customer->addr1 ?? '' }}{{ $customer->addr2 ?? '' }}</div>
            <div style="text-align:right;font-size:12px">
                <div style="font-weight:700;font-size:{{ $sale->font_name ?? 14 }}pt">{{ $company->name ?? '' }}</div>
                <div>登録番号: {{ $company->reg_no ?? '' }}</div>
                <div style="font-size:{{ $sale->font_addr ?? 12 }}pt">{{ $company->zip ?? '' }} {{ $company->addr ?? '' }}</div>
                <div style="font-size:{{ $sale->font_contact ?? 12 }}pt">TEL: {{ $company->tel ?? '' }}</div>
                @if ($sale->staff_name)
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;margin-top:2px">
                    担当: {{ $sale->staff_name }}
                    @if ($sale->files['staff_seal'] ?? null)
                    <img src="{{ $sale->files['staff_seal']['dataUrl'] ?? route('sale.seal', [$sale, 'staff_seal']) }}" alt="担当者印" style="width:32px;height:32px;object-fit:contain">
                    @endif
                </div>
                @endif
            </div>
        </div>
        <table class="inv">
            <tr><th style="width:44%">品目・内容</th><th class="num">税抜金額</th><th style="width:70px;text-align:center">税率</th><th class="num">消費税額</th><th class="num">税込金額</th></tr>
            @foreach ($items as $item)
            @php
                $itemAmount = is_array($item) ? $item['amount'] : $item->amount;
                $itemRate = (int) (is_array($item) ? $item['rate'] : $item->rate);
                $itemName = is_array($item) ? $item['name'] : $item->name;
                $itemTax = intdiv((int) $itemAmount * $itemRate, 100);
                $label = $itemRate === 8 ? '8%' : ($itemRate === 0 ? '-' : '10%');
            @endphp
            <tr>
                <td>{{ $itemName }}{{ $itemRate === 8 ? '※' : '' }}</td>
                <td class="num">¥{{ number_format($itemAmount) }}</td>
                <td style="text-align:center">{{ $label }}</td>
                <td class="num">¥{{ number_format($itemTax) }}</td>
                <td class="num">¥{{ number_format($itemAmount + $itemTax) }}</td>
            </tr>
            @endforeach
            <tr class="inv-total-row"><td colspan="3" style="text-align:right">合計</td><td class="num">¥{{ number_format($totals['tax']) }}</td><td class="num">¥{{ number_format($totals['total']) }}</td></tr>
        </table>
        <div class="inv-breakdown">
            <table>
                @foreach ($rates as $rate)
                @php $group = $rows[$rate]; @endphp
                <tr><td>{{ $rateLabels[$rate] }}</td><td class="num">¥{{ number_format($group['sub']) }}</td><td>消費税</td><td class="num">¥{{ number_format($group['tax']) }}</td></tr>
                @endforeach
            </table>
        </div>
        @if ($hasReduced)
        <div class="inv-note">※は軽減税率対象品目です。</div>
        @endif
        @if ($multiPerRate)
        <div class="inv-note">消費税額は適格請求書等保存方式に基づき、税率ごとに区分した合計額に対して1回のみ端数処理しています(明細行ごとの税額は目安表示です)。</div>
        @endif
        <div class="inv-due">取引年月日: {{ optional($sale->date)->format('Y-m-d') }}</div>
        <div class="inv-bigtotal">ご請求金額：<span class="amt">¥{{ number_format($totals['total']) }}</span>（税込）</div>
        <div class="inv-due">お支払期限: {{ $dueDate->format('Y-m-d') }}</div>
        @if ($sale->inv_memo)
        <div class="inv-note" style="margin-top:8px">{!! nl2br(e($sale->inv_memo)) !!}</div>
        @endif
        <div style="margin-top:18px;font-size:12px">
            <b>お振込先</b><br>{!! nl2br(e($company->bank ?? '')) !!}<br>
            <span class="muted">恐れ入りますが、振込手数料は貴社にてご負担いただけますようお願い申し上げます。<br>ご不明な点がございましたら、お気軽にお問い合わせください。</span>
        </div>
    </div>
</div>
<div class="formfoot no-print" style="justify-content:center">
    <button class="btn ghost" type="button" onclick="closeModal()">閉じる</button>
    @empty($isPreview)
    <button class="btn accent" type="button" onclick="saleInvoicePrint()">印刷 / PDF保存</button>
    @endempty
    @if ($sale->status === '未請求' && empty($isPreview))
    <button class="btn" type="button" onclick="saleInvoiceIssue('{{ $sale->id }}')">請求済にする</button>
    @endif
</div>
@endif
