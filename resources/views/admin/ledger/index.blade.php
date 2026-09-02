@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / 総勘定元帳</div>
@if (session('status'))
<div class="card" style="background:#e8f8ee; color:#1d7a45; margin-bottom:12px">{{ session('status') }}</div>
@endif
@if (session('error'))
<div class="card" style="background:#fff0f0; color:#b22; margin-bottom:12px">{{ session('error') }}</div>
@endif
<h2 class="pagettl">総勘定元帳</h2>
@include('admin.partials.error-summary')
<div class="lgLayout">
    <div class="lgSide">
        @if ($tab === 'jnl')
        <button type="button" class="btn ghost small" onclick="ledgerRowAdd()">+ 1行追加</button>
        @endif
        <form method="POST" action="{{ route('ledger.import') }}" enctype="multipart/form-data">
            @csrf
            <button type="button" class="btn blue small" onclick="document.getElementById('ledgerCsvFile').click()">⬆ CSVインポート</button>
            <input type="file" id="ledgerCsvFile" name="csv_file" accept=".csv" style="display:none" onchange="this.form.submit()">
        </form>
    </div>
    <div class="panel" style="flex:1">
        <div class="tabbar">
            <a href="{{ route('ledger', ['tab' => 'jnl']) }}" class="{{ $tab === 'jnl' ? 'active' : '' }}">仕訳帳</a>
            <a href="{{ route('ledger', ['tab' => 'lg']) }}" class="{{ $tab === 'lg' ? 'active' : '' }}">総勘定元帳</a>
        </div>
        @if ($tab === 'jnl')
            @include('admin.ledger.journal')
        @else
            @include('admin.ledger.accounts')
        @endif
    </div>
</div>
@endsection
