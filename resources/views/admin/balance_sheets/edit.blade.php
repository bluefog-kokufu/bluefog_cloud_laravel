@extends('layouts.app')

@php
    $assetsRows = old('assets', $balanceSheet->assets ?? []);
    $liabsRows = old('liabs', $balanceSheet->liabs ?? []);
    $equityRows = old('equity', $balanceSheet->equity ?? []);
@endphp

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / 貸借対照表</div>
@if (session('status'))
<div class="card" style="background:#e8f8ee; color:#1d7a45; margin-bottom:12px">{{ session('status') }}</div>
@endif
<h2 class="pagettl">貸借対照表</h2>
<div class="panel">
    <form method="POST" action="{{ route('bs.update') }}">
        @csrf
        @method('PUT')
        <div class="sheet-head">
            <div>
                <button type="button" class="btn ghost small" onclick="bsRowAdd('assets')">+ 資産 1行追加</button>
                <button type="button" class="btn ghost small" onclick="bsRowAdd('liabs')">+ 負債 1行追加</button>
                <button type="button" class="btn ghost small" onclick="bsRowAdd('equity')">+ 純資産 1行追加</button>
            </div>
            <span class="ttl" style="margin:0 auto">貸借対照表</span>
            <span class="unit">(単位:円)</span>
        </div>
        <div class="sheet-head">
            日付: <input type="date" name="date" value="{{ old('date', optional($balanceSheet->date)->format('Y-m-d')) }}" style="width:160px">
        </div>
        @error('date')<div class="field-error">{{ $message }}</div>@enderror

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px" id="bsWrap">
            <table class="sheet" id="bsAssetsTable">
                <thead><tr><th colspan="3">資産の部</th></tr></thead>
                <tbody id="bsAssetsBody" data-next-index="{{ count($assetsRows) }}">
                    @foreach ($assetsRows as $i => $row)
                    <tr>
                        <td><input type="text" name="assets[{{ $i }}][name]" value="{{ $row['name'] ?? '' }}"></td>
                        <td style="width:160px"><input type="number" name="assets[{{ $i }}][v]" value="{{ $row['v'] ?? 0 }}" style="text-align:right" oninput="bsRecalcAll()"></td>
                        <td style="width:36px"><button type="button" class="icon-btn" onclick="bsRowDel(this)">🗑</button></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td class="total">資産の部合計</td><td class="total num" id="bsAssetsTotal" style="padding:4px 8px">{{ number_format($totals['assets']) }}</td><td></td></tr>
                </tfoot>
            </table>
            <table class="sheet" id="bsRightTable">
                <thead><tr><th colspan="3">負債の部</th></tr></thead>
                <tbody id="bsLiabsBody" data-next-index="{{ count($liabsRows) }}">
                    @foreach ($liabsRows as $i => $row)
                    <tr>
                        <td><input type="text" name="liabs[{{ $i }}][name]" value="{{ $row['name'] ?? '' }}"></td>
                        <td style="width:160px"><input type="number" name="liabs[{{ $i }}][v]" value="{{ $row['v'] ?? 0 }}" style="text-align:right" oninput="bsRecalcAll()"></td>
                        <td style="width:36px"><button type="button" class="icon-btn" onclick="bsRowDel(this)">🗑</button></td>
                    </tr>
                    @endforeach
                </tbody>
                <tbody>
                    <tr><td class="total">負債の部合計</td><td class="total num" id="bsLiabsTotal" style="padding:4px 8px">{{ number_format($totals['liabs']) }}</td><td></td></tr>
                    <tr><th colspan="3">純資産の部</th></tr>
                </tbody>
                <tbody id="bsEquityBody" data-next-index="{{ count($equityRows) }}">
                    @foreach ($equityRows as $i => $row)
                    <tr>
                        <td><input type="text" name="equity[{{ $i }}][name]" value="{{ $row['name'] ?? '' }}"></td>
                        <td style="width:160px"><input type="number" name="equity[{{ $i }}][v]" value="{{ $row['v'] ?? 0 }}" style="text-align:right" oninput="bsRecalcAll()"></td>
                        <td style="width:36px"><button type="button" class="icon-btn" onclick="bsRowDel(this)">🗑</button></td>
                    </tr>
                    @endforeach
                </tbody>
                <tbody>
                    <tr><td class="total">純資産の部合計</td><td class="total num" id="bsEquityTotal" style="padding:4px 8px">{{ number_format($totals['equity']) }}</td><td></td></tr>
                    <tr><td class="total">負債・純資産の部合計</td><td class="total num" id="bsLiabsEquityTotal" style="padding:4px 8px">{{ number_format($totals['liabsAndEquity']) }}</td><td></td></tr>
                </tbody>
            </table>
        </div>
        <div id="bsBalanceMsg" style="color:{{ $totals['balanced'] ? 'var(--ok)' : 'var(--danger)' }};font-size:12px;margin-top:8px">
            @if ($totals['balanced'])
            ✓ 貸借一致しています。
            @else
            ⚠ 資産合計({{ number_format($totals['assets']) }})と負債・純資産合計({{ number_format($totals['liabsAndEquity']) }})が一致していません。
            @endif
        </div>
        <div class="formfoot">
            <button class="btn" type="submit">保存する</button>
            <a class="btn ghost" href="{{ route('bs.export') }}">CSV保存</a>
        </div>
    </form>
</div>
@endsection
