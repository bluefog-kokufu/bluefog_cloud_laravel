@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / 会計・消費税設定</div>
@if (session('status'))
<div class="card" style="background:#e8f8ee; color:#1d7a45; margin-bottom:12px">{{ session('status') }}</div>
@endif
<h2 class="pagettl">会計 / 端数・消費税設定</h2>
<div class="panel">
    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')
        <div class="card">
            <b style="color:var(--navy)">消費税・端数処理</b>
            <div class="grid2" style="margin-top:10px">
                <div class="field">
                    <label>消費税率(%)</label>
                    <select name="tax_rate">
                        @foreach (\App\Services\SettingsService::TAX_RATES as $rate)
                        <option value="{{ $rate }}" {{ old('tax_rate', $company->tax_rate) == $rate ? 'selected' : '' }}>{{ $rate }}%</option>
                        @endforeach
                    </select>
                    @error('tax_rate')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>端数処理</label>
                    <select name="rounding">
                        @foreach (\App\Services\SettingsService::ROUNDING_OPTIONS as $value => $label)
                        <option value="{{ $value }}" {{ old('rounding', $company->rounding) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('rounding')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="card">
            <b style="color:var(--navy)">自社情報(請求書に記載されます)</b>
            <div class="grid2" style="margin-top:10px">
                <div class="field">
                    <label>会社名</label>
                    <input name="name" value="{{ old('name', $company->name) }}">
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>適格請求書発行事業者 登録番号</label>
                    <input name="reg_no" value="{{ old('reg_no', $company->reg_no) }}">
                </div>
                <div class="field">
                    <label>郵便番号</label>
                    <input name="zip" value="{{ old('zip', $company->zip) }}">
                </div>
                <div class="field">
                    <label>電話番号</label>
                    <input name="tel" value="{{ old('tel', $company->tel) }}">
                </div>
            </div>
            <div class="field">
                <label>住所</label>
                <input name="addr" value="{{ old('addr', $company->addr) }}">
            </div>
            <div class="field">
                <label>振込先</label>
                <input name="bank" value="{{ old('bank', $company->bank) }}">
            </div>
        </div>
        <div class="formfoot">
            <button class="btn" type="submit">保存する</button>
            @unless (app()->environment('production'))
            <button class="btn danger" type="submit" form="reset-demo-form">デモデータを初期化</button>
            @endunless
        </div>
    </form>
    @unless (app()->environment('production'))
    <form id="reset-demo-form" method="POST" action="{{ route('settings.reset-demo') }}" onsubmit="return confirm('すべてのデータを初期状態に戻します。よろしいですか？');">
        @csrf
    </form>
    @endunless
</div>
@endsection
