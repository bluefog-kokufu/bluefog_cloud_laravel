@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / 受注取引一覧</div>
@if (session('status'))
<div class="card" style="background:#e8f8ee; color:#1d7a45; margin-bottom:12px">{{ session('status') }}</div>
@endif
<h2 class="pagettl">受注取引一覧</h2>
@include('admin.partials.error-summary')
<div class="panel">
    <form method="GET" action="{{ route('sale') }}" class="toolbar" style="gap:8px; align-items:center;">
        <input type="text" name="q" placeholder="No・取引先名で検索" value="{{ $filters['q'] ?? '' }}">
        <input type="date" name="from" title="作成日:開始日" value="{{ $filters['from'] ?? '' }}" style="width:150px">
        <input type="date" name="to" title="作成日:終了日" value="{{ $filters['to'] ?? '' }}" style="width:150px">
        <select name="method">
            <option value="">入金方法</option>
            @foreach (\App\Services\SaleService::METHODS as $method)
            <option value="{{ $method }}" {{ ($filters['method'] ?? '') === $method ? 'selected' : '' }}>{{ $method }}</option>
            @endforeach
        </select>
        <select name="status">
            <option value="">ステータス</option>
            @foreach (\App\Services\SaleService::STATUSES as $status)
            <option value="{{ $status }}" {{ ($filters['status'] ?? '') === $status ? 'selected' : '' }}>{{ $status }}</option>
            @endforeach
        </select>
        <button class="btn small" type="submit">絞り込み</button>
        <button class="btn ghost small" type="button" onclick="location.href='{{ route('sale') }}'">リセット</button>
        <span style="flex:1"></span>
        <button class="btn ghost small" type="button" onclick="saleCreate()">取引作成</button>
    </form>

    <form method="POST" action="{{ route('sale.import') }}" enctype="multipart/form-data" class="csvbar">
        @csrf
        <a class="btn yellow small" href="{{ route('sale.export') }}">⬇ データのエクスポート(CSV)</a>
        <button class="btn blue small" type="button" onclick="document.getElementById('saleCsvFile').click()">⬆ CSVインポート</button>
        <input type="file" id="saleCsvFile" name="csv_file" accept=".csv" style="display:none" onchange="this.form.submit()">
        <a class="btn green small" href="{{ route('sale.template') }}">⬇ CSVテンプレート</a>
    </form>

    <div class="card" style="overflow-x:auto">
        <table class="list">
            <thead>
                <tr>
                    <th>No</th>
                    <th>作成日</th>
                    <th>取引先名</th>
                    <th>入金方法</th>
                    <th class="num">請求金額</th>
                    <th class="num">税額</th>
                    <th>請求書</th>
                    <th>作成日時/履歴</th>
                    <th>ステータス</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                <tr>
                    <td class="muted" style="max-width:170px;overflow:hidden;text-overflow:ellipsis">{{ $sale->id }}</td>
                    <td>{{ optional($sale->date)->format('Y.m.d') }}</td>
                    <td>{{ $sale->customer->name ?? '(削除済み)' }}</td>
                    <td>{{ $sale->method }}</td>
                    <td class="num">¥{{ number_format($sale->amount) }}</td>
                    <td class="num">¥{{ number_format($sale->tax) }}</td>
                    <td>
                        @if ($sale->invoiced)
                        <button class="icon-btn" title="請求書を表示" type="button" onclick="saleInvoiceView('{{ $sale->id }}')">⬇</button>
                        <button class="icon-btn" title="請求書を編集" type="button" onclick="location.href='{{ route('sale.invoice.edit', $sale) }}'">✎</button>
                        @else
                        <a href="{{ route('sale.invoice.edit', $sale) }}">請求書作成</a>
                        @endif
                    </td>
                    <td class="muted">{{ $sale->invoiced ? $sale->invoiced->format('Y.m.d H:i') : '' }}</td>
                    <td>
                        <span class="badge {{ match ($sale->status) { '入金済' => 'paid', '請求済' => 'warn', '回収不能' => 'bad', default => 'gray' } }}">{{ $sale->status }}</span>
                    </td>
                    <td>
                        <button class="icon-btn" title="編集" type="button" onclick="saleEdit('{{ $sale->id }}')">✎</button>
                        <button class="icon-btn" title="削除" type="button" onclick="saleDelete('{{ $sale->id }}')">🗑</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="muted">取引がありません。</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pager" style="display:flex; justify-content:center; gap:8px; align-items:center; margin-top:12px;">
            @if ($sales->onFirstPage())
            <button class="btn small" type="button" disabled>&lt;</button>
            @else
            <a class="btn small" href="{{ $sales->previousPageUrl() }}">&lt;</a>
            @endif

            <button class="btn small cur" type="button">{{ $sales->currentPage() }}</button>

            @if ($sales->hasMorePages())
            <a class="btn small" href="{{ $sales->nextPageUrl() }}">&gt;</a>
            @else
            <button class="btn small" type="button" disabled>&gt;</button>
            @endif
        </div>
    </div>
    <div class="muted">CSVインポート: 「取引No」列が同じ行は1つの取引の明細としてまとめて登録されます。取引先名が未登録の場合は顧客情報も自動作成されます。</div>
</div>

@if ($errors->any())
<div id="reopenSaleForm" style="display:none">
    @if ($reopenSale)
    @include('admin.sales.form', ['sale' => $reopenSale, 'customers' => $customers])
    @else
    @include('admin.sales.form', ['sale' => null, 'customers' => $customers])
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tpl = document.getElementById('reopenSaleForm');
        if (tpl) {
            openModal(tpl.innerHTML);
            tpl.remove();
        }
    });
</script>
@endif
@endsection
