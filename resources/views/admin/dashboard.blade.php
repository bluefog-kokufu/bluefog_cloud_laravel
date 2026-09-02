@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / マイページ</div>
<h2 class="pagettl">マイページトップ</h2>
<div class="cards">
    <div class="stat">
        <div class="t">登録顧客数</div>
        <div class="v">{{ $customerCount }} 社</div>
    </div>
    <div class="stat">
        <div class="t">今月の売上(税抜)</div>
        <div class="v">¥{{ number_format($monthlySales) }}</div>
    </div>
    <div class="stat">
        <div class="t">未回収売掛金(税込)</div>
        <div class="v">¥{{ number_format($unpaidReceivables) }}</div>
    </div>
    <div class="stat">
        <div class="t">未払買掛金(税込)</div>
        <div class="v">¥{{ number_format($unpaidPayables) }}</div>
    </div>
</div>
<div class="panel">
    <div class="card">
        <b style="color:var(--navy)">お知らせ</b>
        <ul class="notice" style="margin-top:8px">
            @foreach ($notices as $notice)
            <li>
                <span class="d">{{ $notice['date'] }}</span>
                <b>{{ $notice['title'] }}</b>
                @if (! empty($notice['message']))
                — {{ $notice['message'] }}
                @endif
            </li>
            @endforeach
        </ul>
    </div>
    <div class="card">
        <b style="color:var(--navy)">クイックメニュー</b>
        <div class="toolbar" style="margin-top:10px">
            <a class="btn accent small" href="{{ route('customer') }}">顧客管理</a>
            <a class="btn accent small" href="{{ route('sale') }}">取引管理(売上)</a>
            <a class="btn accent small" href="{{ route('purchase') }}">取引書類(仕入)</a>
            <a class="btn accent small" href="{{ route('ledger') }}">総勘定元帳</a>
            <a class="btn accent small" href="{{ route('bs') }}">財務三表</a>
        </div>
    </div>
</div>
@endsection