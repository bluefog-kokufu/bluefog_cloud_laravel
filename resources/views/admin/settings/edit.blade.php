@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / 会計・消費税設定</div>
@if (session('status'))
<div class="card" style="background:#e8f8ee; color:#1d7a45; margin-bottom:12px">{{ session('status') }}</div>
@endif
<h2 class="pagettl">会計 / 端数・消費税設定</h2>
@include('admin.partials.error-summary')
<div class="panel">
    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')
        <div class="card">
            <b style="color:var(--navy)">消費税・端数処理</b>
            <div class="grid2" style="margin-top:10px">
                <div class="field">
                    <label>消費税率(%)<span class="req">必須</span></label>
                    <select name="tax_rate">
                        @foreach (\App\Services\SettingsService::TAX_RATES as $rate)
                        <option value="{{ $rate }}" {{ old('tax_rate', $company->tax_rate) == $rate ? 'selected' : '' }}>{{ $rate }}%</option>
                        @endforeach
                    </select>
                    @error('tax_rate')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>端数処理<span class="req">必須</span></label>
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
                    <label>会社名<span class="req">必須</span></label>
                    <input name="name" value="{{ old('name', $company->name) }}">
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>適格請求書発行事業者 登録番号<span class="req">必須</span></label>
                    <input name="reg_no" value="{{ old('reg_no', $company->reg_no) }}">
                    <div class="hint">例: T1234567890123(「T」+13桁の数字)</div>
                    @error('reg_no')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>郵便番号<span class="req">必須</span></label>
                    <input name="zip" value="{{ old('zip', $company->zip) }}">
                    <div class="hint">例: 1000001 または 100-0001</div>
                    @error('zip')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>電話番号<span class="req">必須</span></label>
                    <input name="tel" value="{{ old('tel', $company->tel) }}">
                    <div class="hint">例: 03-0000-0000</div>
                    @error('tel')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="field">
                <label>住所<span class="req">必須</span></label>
                <input name="addr" value="{{ old('addr', $company->addr) }}">
                @error('addr')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>振込先<span class="opt">任意</span></label>
                <input name="bank" value="{{ old('bank', $company->bank) }}">
                @error('bank')<div class="field-error">{{ $message }}</div>@enderror
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
