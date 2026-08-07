@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / 取引書類一覧</div>
@if (session('status'))
<div class="card" style="background:#e8f8ee; color:#1d7a45; margin-bottom:12px">{{ session('status') }}</div>
@endif
<h2 class="pagettl">取引書類一覧(仕入・アップロード)</h2>
<div class="panel">
    <form method="GET" action="{{ route('purchase') }}" class="toolbar" style="gap:8px; align-items:center;">
        <input type="text" name="q" placeholder="No・取引先名で検索" value="{{ $filters['q'] ?? '' }}">
        <input type="date" name="from" title="発行日:開始日" value="{{ $filters['from'] ?? '' }}" style="width:150px">
        <input type="date" name="to" title="発行日:終了日" value="{{ $filters['to'] ?? '' }}" style="width:150px">
        <select name="method">
            <option value="">入金方法</option>
            @foreach (\App\Services\PurchaseService::METHODS as $method)
            <option value="{{ $method }}" {{ ($filters['method'] ?? '') === $method ? 'selected' : '' }}>{{ $method }}</option>
            @endforeach
        </select>
        <select name="status">
            <option value="">ステータス</option>
            @foreach (\App\Services\PurchaseService::STATUSES as $status)
            <option value="{{ $status }}" {{ ($filters['status'] ?? '') === $status ? 'selected' : '' }}>{{ $status }}</option>
            @endforeach
        </select>
        <button class="btn small" type="submit">絞り込み</button>
        <button class="btn ghost small" type="button" onclick="location.href='{{ route('purchase') }}'">リセット</button>
        <span style="flex:1"></span>
        <button class="btn ghost small" type="button" onclick="purchaseCreate()">取引作成</button>
    </form>

    <form method="POST" action="{{ route('purchase.import') }}" enctype="multipart/form-data" class="csvbar">
        @csrf
        <a class="btn yellow small" href="{{ route('purchase.export') }}">⬇ データのエクスポート(CSV)</a>
        <button class="btn blue small" type="button" onclick="document.getElementById('purchaseCsvFile').click()">⬆ CSVインポート</button>
        <input type="file" id="purchaseCsvFile" name="csv_file" accept=".csv" style="display:none" onchange="this.form.submit()">
        <a class="btn green small" href="{{ route('purchase.template') }}">⬇ CSVテンプレート</a>
    </form>

    <div class="card" style="overflow-x:auto">
        <table class="list">
            <thead>
                <tr>
                    <th>No</th>
                    <th>取引年月日</th>
                    <th>取引先名</th>
                    <th>入金方法</th>
                    <th class="num">取引金額</th>
                    <th class="num">税額</th>
                    <th>アップロード</th>
                    <th>ステータス</th>
                    @foreach (\App\Services\PurchaseService::DOCS as $label)
                    <th>{{ $label }}</th>
                    @endforeach
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $purchase)
                <tr>
                    <td class="muted" style="max-width:170px;overflow:hidden;text-overflow:ellipsis">{{ $purchase->id }}</td>
                    <td>{{ optional($purchase->date)->format('Y.m.d') }}</td>
                    <td>{{ $purchase->customer->name ?? '(削除済み)' }}</td>
                    <td>{{ $purchase->method }}</td>
                    <td class="num">¥{{ number_format($purchase->amount) }}</td>
                    <td class="num">¥{{ number_format($purchase->tax) }}</td>
                    <td>{{ optional($purchase->up)->format('Y.m.d') }}</td>
                    <td><span class="badge {{ $purchase->status === '支払い済' ? 'warn' : 'gray' }}">{{ $purchase->status }}</span></td>
                    @foreach (\App\Services\PurchaseService::DOCS as $key => $label)
                    <td>
                        @if (! empty($purchase->files[$key]))
                        <a href="{{ route('purchase.file', [$purchase, $key]) }}" target="_blank">PDF</a>
                        @endif
                    </td>
                    @endforeach
                    <td>
                        <button class="icon-btn" title="編集" type="button" onclick="purchaseEdit('{{ $purchase->id }}')">✎</button>
                        <button class="icon-btn" title="削除" type="button" onclick="purchaseDelete('{{ $purchase->id }}')">🗑</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 9 + count(\App\Services\PurchaseService::DOCS) }}" class="muted">取引書類がありません。</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pager" style="display:flex; justify-content:center; gap:8px; align-items:center; margin-top:12px;">
            @if ($purchases->onFirstPage())
            <button class="btn small" type="button" disabled>&lt;</button>
            @else
            <a class="btn small" href="{{ $purchases->previousPageUrl() }}">&lt;</a>
            @endif

            <button class="btn small cur" type="button">{{ $purchases->currentPage() }}</button>

            @if ($purchases->hasMorePages())
            <a class="btn small" href="{{ $purchases->nextPageUrl() }}">&gt;</a>
            @else
            <button class="btn small" type="button" disabled>&gt;</button>
            @endif
        </div>
    </div>
    <div class="muted">アップロードされた書類には受領後すみやかにタイムスタンプ(アップロード日時)が付与され、「取引年月日」「取引金額」「取引先」で検索できます(電子帳簿保存法対応)。<br>CSVインポート: 書類ファイルはCSVでは取り込めないため、登録後に各取引の編集からアップロードしてください。</div>
</div>

@if ($errors->any())
<div id="reopenPurchaseForm" style="display:none">
    @include('admin.purchases.form', ['purchase' => $reopenPurchase, 'customers' => $customers])
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tpl = document.getElementById('reopenPurchaseForm');
        if (tpl) {
            openModal(tpl.innerHTML);
            tpl.remove();
        }
    });
</script>
@endif
@endsection
