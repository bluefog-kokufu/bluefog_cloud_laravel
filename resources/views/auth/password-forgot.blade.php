<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bluefog Cloud</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div id="loginView">
        <div class="login-card">
            <h1>Bluefog Cloud</h1>
            <div class="sub">パスワード再設定</div>

            @if (session('status'))
            <div class="err" style="color:var(--navy)">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.forgot.send') }}">
                @csrf
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="user@user.com" autofocus autocomplete="username">

                @if ($errors->any())
                <div class="err">{{ $errors->first() }}</div>
                @endif

                <button class="btn block" type="submit">メールを送信</button>
                <div class="login-links">
                    <a href="{{ route('login') }}">ログイン画面に戻る</a>
                </div>
            </form>
        </div>
    </div>

    @vite(['resources/js/app.js'])

</body>

</html>
