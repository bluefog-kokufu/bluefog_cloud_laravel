@extends('layouts.app')

@php
    $opRows = old('operating', $cashFlowStatement->operating ?? []);
    $invRows = old('investing', $cashFlowStatement->investing ?? []);
    $finRows = old('financing', $cashFlowStatement->financing ?? []);
    $tri = fn ($n) => $n < 0 ? '△'.number_format(abs($n)) : number_format($n);
@endphp

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / キャッシュフロー計算書</div>
@if (session('status'))
<div class="card" style="background:#e8f8ee; color:#1d7a45; margin-bottom:12px">{{ session('status') }}</div>
@endif
<h2 class="pagettl">キャッシュフロー計算書</h2>
<div class="panel">
    <form method="POST" action="{{ route('cf.update') }}">
        @csrf
        @method('PUT')
        <div class="sheet-head">
            <div>
                <button type="button" class="btn ghost small" onclick="cfRowAdd('operating')">+ 営業活動 1行追加</button>
                <button type="button" class="btn ghost small" onclick="cfRowAdd('investing')">+ 投資活動 1行追加</button>
                <button type="button" class="btn ghost small" onclick="cfRowAdd('financing')">+ 財務活動 1行追加</button>
            </div>
            <span class="ttl" style="margin:0 auto">キャッシュフロー計算書</span>
            <span class="unit">(単位:円)</span>
        </div>
        <div class="sheet-head">
            自: <input type="date" name="period_from" value="{{ old('period_from', optional($cashFlowStatement->period_from)->format('Y-m-d')) }}" style="width:150px">
            至: <input type="date" name="period_to" value="{{ old('period_to', optional($cashFlowStatement->period_to)->format('Y-m-d')) }}" style="width:150px">
        </div>
        @error('period_from')<div class="field-error">{{ $message }}</div>@enderror
        @error('period_to')<div class="field-error">{{ $message }}</div>@enderror

        <table class="sheet" id="cfTable">
            <thead><tr><th colspan="3">Ⅰ 営業活動によるキャッシュ・フロー</th></tr></thead>
            <tbody id="cfOperatingBody" data-next-index="{{ count($opRows) }}">
                @foreach ($opRows as $i => $row)
                <tr>
                    <td style="padding-left:24px"><input type="text" name="operating[{{ $i }}][name]" value="{{ $row['name'] ?? '' }}"></td>
                    <td style="width:180px"><input type="text" inputmode="numeric" name="operating[{{ $i }}][v]" value="{{ number_format($row['v'] ?? 0) }}" style="text-align:right" oninput="cfRecalcAll()"></td>
                    <td style="width:36px"><button type="button" class="icon-btn" onclick="cfRowDel(this)">🗑</button></td>
                </tr>
                @endforeach
            </tbody>
            <tbody>
                <tr><td class="total">営業活動によるキャッシュ・フロー</td><td class="total num" id="cfOperatingTotal" style="padding:4px 8px">{{ $tri($totals['operating']) }}</td><td></td></tr>
                <tr><th colspan="3">Ⅱ 投資活動によるキャッシュ・フロー</th></tr>
            </tbody>
            <tbody id="cfInvestingBody" data-next-index="{{ count($invRows) }}">
                @foreach ($invRows as $i => $row)
                <tr>
                    <td style="padding-left:24px"><input type="text" name="investing[{{ $i }}][name]" value="{{ $row['name'] ?? '' }}"></td>
                    <td style="width:180px"><input type="text" inputmode="numeric" name="investing[{{ $i }}][v]" value="{{ number_format($row['v'] ?? 0) }}" style="text-align:right" oninput="cfRecalcAll()"></td>
                    <td style="width:36px"><button type="button" class="icon-btn" onclick="cfRowDel(this)">🗑</button></td>
                </tr>
                @endforeach
            </tbody>
            <tbody>
                <tr><td class="total">投資活動によるキャッシュ・フロー</td><td class="total num" id="cfInvestingTotal" style="padding:4px 8px">{{ $tri($totals['investing']) }}</td><td></td></tr>
                <tr><th colspan="3">Ⅲ 財務活動によるキャッシュ・フロー</th></tr>
            </tbody>
            <tbody id="cfFinancingBody" data-next-index="{{ count($finRows) }}">
                @foreach ($finRows as $i => $row)
                <tr>
                    <td style="padding-left:24px"><input type="text" name="financing[{{ $i }}][name]" value="{{ $row['name'] ?? '' }}"></td>
                    <td style="width:180px"><input type="text" inputmode="numeric" name="financing[{{ $i }}][v]" value="{{ number_format($row['v'] ?? 0) }}" style="text-align:right" oninput="cfRecalcAll()"></td>
                    <td style="width:36px"><button type="button" class="icon-btn" onclick="cfRowDel(this)">🗑</button></td>
                </tr>
                @endforeach
            </tbody>
            <tbody>
                <tr><td class="total">財務活動によるキャッシュ・フロー</td><td class="total num" id="cfFinancingTotal" style="padding:4px 8px">{{ $tri($totals['financing']) }}</td><td></td></tr>
                <tr><td class="total">Ⅳ 現金及び現金同等物の増減額</td><td class="total num" id="cfDeltaTotal" style="padding:4px 8px">{{ $tri($totals['delta']) }}</td><td></td></tr>
                <tr><td class="total">Ⅴ 現金及び現金同等物の期首残高</td><td style="width:180px"><input type="text" inputmode="numeric" name="beginning_balance" id="cfBeginningBalance" value="{{ number_format(old('beginning_balance', $cashFlowStatement->beginning_balance)) }}" style="text-align:right" oninput="cfRecalcAll()"></td><td></td></tr>
                <tr><td class="total">Ⅵ 現金及び現金同等物の期末残高</td><td class="total num" id="cfEndingTotal" style="padding:4px 8px">{{ $tri($totals['endingBalance']) }}</td><td></td></tr>
            </tbody>
        </table>
        @error('beginning_balance')<div class="field-error">{{ $message }}</div>@enderror
        <div class="formfoot">
            <button class="btn" type="submit">保存する</button>
            <a class="btn ghost" href="{{ route('cf.export') }}">CSV保存</a>
        </div>
    </form>
</div>
@endsection
