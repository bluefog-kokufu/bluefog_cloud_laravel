@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / 顧客一覧</div>
@if (session('status'))
<div class="card" style="background:#e8f8ee; color:#1d7a45; margin-bottom:12px">{{ session('status') }}</div>
@endif
<h2 class="pagettl">顧客一覧</h2>
<div class="panel">
    <form method="GET" action="{{ route('customer') }}" class="toolbar" style="gap:8px; align-items:center;">
        <input type="text" name="q" class="input" placeholder="会社名・担当者・メール等で検索" value="{{ old('q', $query ?? request('q')) }}">
        <button class="btn small" type="submit">検索</button>
        <button class="btn ghost small" type="button" onclick="location.href='{{ route('customer') }}'">リセット</button>
        <span style="flex:1"></span>
        <a class="btn ghost small" href="{{ route('customer.create') }}">顧客作成</a>
    </form>

    <form method="POST" action="{{ route('customer.import') }}" enctype="multipart/form-data" class="csvbar">
        @csrf
        <a class="btn yellow small" href="{{ route('customer.export') }}">⬇ データのエクスポート(CSV)</a>
        <button class="btn blue small" type="button" onclick="document.getElementById('customerCsvFile').click()">⬆ CSVインポート</button>
        <input type="file" id="customerCsvFile" name="csv_file" accept=".csv" style="display:none" onchange="this.form.submit()">
        <a class="btn green small" href="{{ route('customer.template') }}">⬇ CSVテンプレート</a>
    </form>

    <div class="card" style="overflow-x:auto">
        <table class="list">
            <thead>
                <tr>
                    <th>No</th>
                    <th>会社名</th>
                    <th>電話番号</th>
                    <th>担当者名</th>
                    <th>メールアドレス</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                <tr>
                    <td class="muted" style="max-width:170px;overflow:hidden;text-overflow:ellipsis">{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->tel ?? '-' }}</td>
                    <td>{{ $customer->person ?? '-' }}</td>
                    <td>{{ $customer->email ?? '-' }}</td>
                    <td>
                        <button class="icon-btn" title="編集" type="button" onclick="customerEdit('{{ $customer->id }}')">✎</button>
                        <button class="icon-btn" title="削除" type="button" onclick="customerDelete('{{ $customer->id }}')">🗑</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="muted">該当する顧客がありません。</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pager" style="display:flex; justify-content:center; gap:8px; align-items:center; margin-top:12px;">
            @if ($customers->onFirstPage())
            <button class="btn small" type="button" disabled>&lt;</button>
            @else
            <a class="btn small" href="{{ $customers->previousPageUrl() }}">&lt;</a>
            @endif

            <button class="btn small cur" type="button">{{ $customers->currentPage() }}</button>

            @if ($customers->hasMorePages())
            <a class="btn small" href="{{ $customers->nextPageUrl() }}">&gt;</a>
            @else
            <button class="btn small" type="button" disabled>&gt;</button>
            @endif
        </div>
    </div>
</div>
@endsection