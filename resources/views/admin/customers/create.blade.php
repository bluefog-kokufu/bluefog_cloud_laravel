@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / <a href="{{ route('customer') }}">顧客一覧</a> / 顧客作成</div>
<h2 class="pagettl">顧客作成</h2>
@if ($errors->any())
<div class="card" style="background:#fff0f0; color:#b22; margin-bottom:12px;">
    <ul style="margin:0; padding-left:18px;">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="panel">
    <form method="POST" action="{{ route('customer.store') }}">
        @csrf
        <div class="card">
            <div class="field"><label><span class="req">必須</span>会社名</label><input type="text" name="name" value="{{ old('name') }}">@error('name')<div class="error">{{ $message }}</div>@enderror</div>
            <div class="field"><label><span class="req">必須</span>顧客タイプ</label>
                <div style="display:flex;gap:18px;align-items:center;padding:8px 0">
                    @foreach(['受注取引管理', '発注取引管理', '両方で使用する'] as $type)
                    <label style="display:flex;align-items:center;gap:6px;font-weight:400;font-size:13px;color:var(--text)">
                        <input type="radio" name="type" value="{{ $type }}" style="width:auto" {{ old('type', '受注取引管理') === $type ? 'checked' : '' }}> {{ $type }}
                    </label>
                    @endforeach
                </div>
                @error('type')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field"><label>郵便番号</label><input type="text" name="zip" value="{{ old('zip') }}" placeholder="1000001"></div>
            <div class="field"><label>都道府県</label><input type="text" name="pref" value="{{ old('pref') }}"></div>
            <div class="field"><label>住所(市区町村・丁番地)</label><input type="text" name="addr1" value="{{ old('addr1') }}"></div>
            <div class="field"><label>住所2(建物名・部屋番号)</label><input type="text" name="addr2" value="{{ old('addr2') }}"></div>
            <div class="field"><label>電話番号</label><input type="text" name="tel" value="{{ old('tel') }}"></div>
            <div class="field"><label>携帯電話番号</label><input type="text" name="mobile" value="{{ old('mobile') }}"></div>
            <div class="field"><label>ファックス番号</label><input type="text" name="fax" value="{{ old('fax') }}"></div>
            <div class="field"><label>ウェブサイトURL</label><input type="text" name="url" value="{{ old('url') }}"></div>
            <div class="field"><label>担当者名</label><input type="text" name="person" value="{{ old('person') }}"></div>
            <div class="field"><label>メールアドレス</label><input type="email" name="email" value="{{ old('email') }}"></div>
            <div class="field"><label>メモ</label><textarea name="memo" rows="4">{{ old('memo') }}</textarea></div>
        </div>
        <div class="toolbar" style="justify-content:flex-end; gap:10px;">
            <a class="btn ghost" href="{{ route('customer') }}">キャンセル</a>
            <button class="btn" type="submit">作成</button>
        </div>
    </form>
</div>
@endsection