@extends('layouts.app')

@section('content')
<h2 class="pagettl">マイページトップ</h2>
<div class="cards">
    <div class="stat">
        <div class="t">登録顧客数</div>
        <div class="v">${db.customers.length} 社</div>
    </div>
    <div class="stat">
        <div class="t">今月の売上(税抜)</div>
        <div class="v">${yen(ms)}</div>
    </div>
    <div class="stat">
        <div class="t">未回収売掛金(税込)</div>
        <div class="v">${yen(ar)}</div>
    </div>
    <div class="stat">
        <div class="t">未払買掛金(税込)</div>
        <div class="v">${yen(ap)}</div>
    </div>
</div>
<div class="panel">
    <div class="card">
        <b style="color:var(--navy)">お知らせ</b>
        <ul class="notice" style="margin-top:8px">
            @foreach ($notices as $notice)
            <li>
                <span class="d">{{ $notice['date'] }}</span>
                @if (! empty($notice['link']))
                <a href="{{ $notice['link'] }}" target="_blank">
                    <b>{{ $notice['title'] }}</b>
                    @if (! empty($notice['message']))
                    — {{ $notice['message'] }}
                    @endif
                </a>
                @if (! empty($notice['pdf']))
                <a href="{{ $notice['pdf'] }}" target="_blank" class="muted">(PDF版)</a>
                @endif
                @else
                {{ $notice['title'] }}
                @endif
            </li>
            @endforeach
        </ul>
    </div>
    <div class="card">
        <b style="color:var(--navy)">クイックメニュー</b>
        <div class="toolbar" style="margin-top:10px">
            <button class="btn accent small" onclick="show('customers')">顧客管理</button>
            <button class="btn accent small" onclick="show('sales')">取引管理(売上)</button>
            <button class="btn accent small" onclick="show('purchases')">取引書類(仕入)</button>
            <button class="btn accent small" onclick="show('ledger')">総勘定元帳</button>
            <button class="btn accent small" onclick="show('bs')">財務三表</button>
            <a class="btn ghost small" href="{{ route('admin.notices.index') }}">お知らせ管理</a>
        </div>
    </div>
</div>
@endsection