@extends('layouts.app')

@section('content')
<div class="crumb"><a href="{{ route('dashboard') }}">ホーム</a> / プロフィール</div>
<h2 class="pagettl">プロフィール</h2>
@include('admin.partials.error-summary')
<div class="panel">
    <form method="POST" action="{{ route('profile.update') }}" class="card">
        @csrf
        <div class="secttl">プロフィール編集</div>

        @if (session('profile_success'))
        <div class="success-inline">{{ session('profile_success') }}</div>
        @endif

        <div class="field">
            <label for="pr_name">氏名</label>
            <input id="pr_name" name="name" value="{{ old('name', $user->name) }}">
            @error('name')
            <div class="field-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="field">
            <label for="pr_email">メールアドレス(ログインID)</label>
            <input id="pr_email" name="email" type="email" value="{{ old('email', $user->email) }}">
            @error('email')
            <div class="field-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="formfoot">
            <button class="btn" type="submit">保存する</button>
        </div>
    </form>

    <form method="POST" action="{{ route('profile.password') }}" class="card">
        @csrf
        <div class="secttl">パスワード変更</div>

        @if (session('password_success'))
        <div class="success-inline">{{ session('password_success') }}</div>
        @endif

        <div class="grid2" style="margin-top:10px">
            <div class="field">
                <label for="current_password">現在のパスワード</label>
                <input type="password" id="current_password" name="current_password">
                @error('current_password')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="new_password">新しいパスワード</label>
                <input type="password" id="new_password" name="new_password">
                @error('new_password')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="formfoot">
            <button class="btn" type="submit">パスワードを変更する</button>
        </div>
    </form>
</div>
@endsection