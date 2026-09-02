@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / <a href="{{ route('customer') }}">顧客一覧</a> / 顧客作成</div>
<h2 class="pagettl">顧客作成</h2>
@include('admin.partials.error-summary')
<div class="panel">
    <form method="POST" action="{{ route('customer.store') }}">
        @csrf
        <div class="card">
            <div class="field"><label>会社名<span class="req">必須</span></label><input type="text" name="name" value="{{ old('name') }}">@error('name')<div class="error">{{ $message }}</div>@enderror</div>
            <div class="field"><label>顧客タイプ<span class="req">必須</span></label>
                <div style="display:flex;gap:18px;align-items:center;padding:8px 0">
                    @foreach(['受注取引管理', '発注取引管理', '両方で使用する'] as $type)
                    <label style="display:flex;align-items:center;gap:6px;font-weight:400;font-size:13px;color:var(--text)">
                        <input type="radio" name="type" value="{{ $type }}" style="width:auto" {{ old('type', '受注取引管理') === $type ? 'checked' : '' }}> {{ $type }}
                    </label>
                    @endforeach
                </div>
                @error('type')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>郵便番号<span class="opt">任意</span></label>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input type="text" id="zip" name="zip" value="{{ old('zip') }}" placeholder="1000001" style="flex:1;">
                    <button type="button" class="btn small" onclick="fillAddressFromZip()">自動入力アドレス</button>
                </div>
                <div class="hint">例: 1000001 または 100-0001</div>
                <div id="zipMsg" class="muted" style="margin-top:4px;"></div>
            </div>
            <div class="field">
                <label>都道府県<span class="opt">任意</span></label>
                <select id="pref" name="pref">
                    <option value="">選択してください</option>
                    @foreach (\App\Http\Controllers\Admin\CustomerController::PREFS as $pref)
                    <option value="{{ $pref }}" {{ old('pref') === $pref ? 'selected' : '' }}>{{ $pref }}</option>
                    @endforeach
                </select>
                @error('pref')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field"><label>住所(市区町村・丁番地)<span class="opt">任意</span></label><input type="text" id="addr1" name="addr1" value="{{ old('addr1') }}"></div>
            <div class="field"><label>住所2(建物名・部屋番号)<span class="opt">任意</span></label><input type="text" name="addr2" value="{{ old('addr2') }}"></div>
            <div class="field">
                <label>電話番号<span class="opt">任意</span></label>
                <input type="text" name="tel" value="{{ old('tel') }}">
                <div class="hint">半角数字とハイフンのみ入力可(例: 090-1234-5678)</div>
                @error('tel')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>携帯電話番号<span class="opt">任意</span></label>
                <input type="text" name="mobile" value="{{ old('mobile') }}">
                <div class="hint">半角数字とハイフンのみ入力可(例: 090-1234-5678)</div>
                @error('mobile')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>ファックス番号<span class="opt">任意</span></label>
                <input type="text" name="fax" value="{{ old('fax') }}">
                <div class="hint">半角数字とハイフンのみ入力可(例: 03-0000-0001)</div>
                @error('fax')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>ウェブサイトURL<span class="opt">任意</span></label>
                <input type="url" name="url" value="{{ old('url') }}" placeholder="https://example.com">
                <div class="hint">http:// または https:// から始まるURL形式</div>
                @error('url')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field"><label>担当者名<span class="opt">任意</span></label><input type="text" name="person" value="{{ old('person') }}"></div>
            <div class="field">
                <label>メールアドレス<span class="opt">任意</span></label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="user@example.com">
                <div class="hint">例: user@example.com</div>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field"><label>メモ<span class="opt">任意</span></label><textarea name="memo" rows="4">{{ old('memo') }}</textarea></div>
        </div>
        <div class="toolbar" style="justify-content:flex-end; gap:10px;">
            <a class="btn ghost" href="{{ route('customer') }}">キャンセル</a>
            <button class="btn" type="submit">作成</button>
        </div>
    </form>
</div>
@endsection