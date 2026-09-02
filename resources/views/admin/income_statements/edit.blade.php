@extends('layouts.app')

@php
    $rows = old('rows', $incomeStatement->rows ?? []);
    $profit = $totals['profit'];
@endphp

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / 損益計算書</div>
@if (session('status'))
<div class="card" style="background:#e8f8ee; color:#1d7a45; margin-bottom:12px">{{ session('status') }}</div>
@endif
<h2 class="pagettl">損益計算書</h2>
@include('admin.partials.error-summary')
<div class="panel">
    <form method="POST" action="{{ route('pl.update') }}">
        @csrf
        @method('PUT')
        <div class="sheet-head">
            <button type="button" class="btn ghost small" onclick="plRowAdd()">+ 1行追加</button>
            <span class="ttl" style="margin:0 auto">損益計算書</span>
            <span class="unit">(単位:円)</span>
        </div>
        <div class="sheet-head">
            自: <input type="date" name="period_from" value="{{ old('period_from', optional($incomeStatement->period_from)->format('Y-m-d')) }}" style="width:150px">
            至: <input type="date" name="period_to" value="{{ old('period_to', optional($incomeStatement->period_to)->format('Y-m-d')) }}" style="width:150px">
        </div>
        @error('period_from')<div class="field-error">{{ $message }}</div>@enderror
        @error('period_to')<div class="field-error">{{ $message }}</div>@enderror

        <table class="sheet" id="plTable">
            <thead><tr><th>科目</th><th style="width:120px">区分</th><th style="width:180px">金額</th><th style="width:36px"></th></tr></thead>
            <tbody id="plItemsBody" data-next-index="{{ count($rows) }}">
                @foreach ($rows as $i => $row)
                <tr>
                    <td><input type="text" name="rows[{{ $i }}][name]" value="{{ $row['name'] ?? '' }}"></td>
                    <td>
                        <select name="rows[{{ $i }}][type]" onchange="plRecalcAll()">
                            @foreach (\App\Services\IncomeStatementService::TYPES as $type)
                            <option value="{{ $type }}" {{ ($row['type'] ?? '費用') === $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" inputmode="numeric" name="rows[{{ $i }}][v]" value="{{ number_format($row['v'] ?? 0) }}" style="text-align:right" oninput="plRecalcAll()"></td>
                    <td><button type="button" class="icon-btn" onclick="plRowDel(this)">🗑</button></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td class="total" colspan="2">収益合計</td><td class="total num" id="plRevenueTotal" style="padding:4px 8px">{{ number_format($totals['revenue']) }}</td><td></td></tr>
                <tr><td class="total" colspan="2">費用合計</td><td class="total num" id="plExpenseTotal" style="padding:4px 8px">{{ number_format($totals['expense']) }}</td><td></td></tr>
                <tr><td class="total" colspan="2">当期純利益</td><td class="total num" id="plProfitTotal" style="padding:4px 8px">{{ $profit < 0 ? '△'.number_format(abs($profit)) : number_format($profit) }}</td><td></td></tr>
            </tfoot>
        </table>
        <div class="formfoot">
            <button class="btn" type="submit">保存する</button>
            <a class="btn ghost" href="{{ route('pl.export') }}">CSV保存</a>
        </div>
    </form>
</div>
@endsection
