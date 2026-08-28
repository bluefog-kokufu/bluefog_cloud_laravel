@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / 支払通知書一覧</div>
@if (session('status'))
<div class="card" style="background:#e8f8ee; color:#1d7a45; margin-bottom:12px">{{ session('status') }}</div>
@endif
<h2 class="pagettl">支払通知書一覧</h2>
<div class="panel">
    <form method="GET" action="{{ route('paynotice') }}" class="toolbar" style="gap:8px; align-items:center;">
        支払日: <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" style="width:150px">
        〜 <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" style="width:150px">
        <button class="btn small" type="submit">絞り込み</button>
        <button class="btn ghost small" type="button" onclick="location.href='{{ route('paynotice') }}'">リセット</button>
        <span style="flex:1"></span>
        <a class="btn ghost small" href="{{ route('paynotice.create') }}">支払通知書作成</a>
    </form>

    <div class="card" style="overflow-x:auto">
        <table class="list">
            <thead>
                <tr>
                    <th>通知番号</th>
                    <th>件名</th>
                    <th>支払日</th>
                    <th class="num">合計金額</th>
                    <th>作成日</th>
                    <th>取引先</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($paymentNotices as $paymentNotice)
                <tr>
                    <td><a onclick="paynoticeView('{{ $paymentNotice->id }}')">{{ $paymentNotice->id }}</a></td>
                    <td>{{ $paymentNotice->title }}</td>
                    <td>{{ optional($paymentNotice->pay_date)->format('Y.m.d') }}</td>
                    <td class="num">¥{{ number_format($paymentNotice->totals['total']) }}</td>
                    <td>{{ optional($paymentNotice->created_at)->format('Y.m.d') }}</td>
                    <td>{{ $paymentNotice->customer->name ?? '(削除済み)' }}</td>
                    <td>
                        <button class="icon-btn" title="表示" type="button" onclick="paynoticeView('{{ $paymentNotice->id }}')">📄</button>
                        <a class="icon-btn" title="編集" href="{{ route('paynotice.edit', $paymentNotice) }}">✎</a>
                        <button class="icon-btn" title="削除" type="button" onclick="paynoticeDelete('{{ $paymentNotice->id }}')">🗑</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="muted">データがありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pager" style="display:flex; justify-content:center; gap:8px; align-items:center; margin-top:12px;">
            @if ($paymentNotices->onFirstPage())
            <button class="btn small" type="button" disabled>&lt;</button>
            @else
            <a class="btn small" href="{{ $paymentNotices->previousPageUrl() }}">&lt;</a>
            @endif

            <button class="btn small cur" type="button">{{ $paymentNotices->currentPage() }}</button>

            @if ($paymentNotices->hasMorePages())
            <a class="btn small" href="{{ $paymentNotices->nextPageUrl() }}">&gt;</a>
            @else
            <button class="btn small" type="button" disabled>&gt;</button>
            @endif
        </div>
    </div>
</div>
@endsection
