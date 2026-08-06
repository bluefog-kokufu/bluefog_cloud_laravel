@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / 顧客一覧</div>
<h2 class="pagettl">顧客一覧</h2>
<div class="panel">
    <form method="GET" action="{{ route('customer') }}" class="toolbar" style="gap:8px; align-items:center;">
        <input type="text" name="q" class="input" placeholder="会社名・担当者・メール等で検索" value="{{ old('q', $query ?? request('q')) }}">
        <button class="btn small" type="submit">検索</button>
        <button class="btn ghost small" type="button" onclick="location.href='{{ route('customer') }}'">リセット</button>
        <span style="flex:1"></span>
        <button class="btn ghost small" type="button" onclick="alert('顧客作成機能は未実装です。')">顧客作成</button>
    </form>

    <div class="csvbar">
        <button class="btn yellow small" type="button" onclick="alert('CSVエクスポート機能は未実装です。')">⬇ データのエクスポート(CSV)</button>
        <button class="btn blue small" type="button" onclick="alert('CSVインポート機能は未実装です。')">⬆ CSVインポート</button>
        <button class="btn green small" type="button" onclick="alert('CSVテンプレート機能は未実装です。')">⬇ CSVテンプレート</button>
    </div>

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
                        <button class="icon-btn" title="編集" type="button" onclick="alert('編集機能は未実装です。')">✎</button>
                        <button class="icon-btn" title="削除" type="button" onclick="alert('削除機能は未実装です。')">🗑</button>
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