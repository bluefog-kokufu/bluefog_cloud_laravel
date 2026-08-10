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
            <div class="sub">新しいパスワードを設定</div>

            <form method="POST" action="{{ route('password.reset.submit') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="token" value="{{ $token }}">

                <label for="password">新しいパスワード</label>
                <input id="password" type="password" name="password" placeholder="8文字以上" autofocus autocomplete="new-password">

                <label for="password_confirmation">新しいパスワード（確認）</label>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="確認のため再入力" autocomplete="new-password">

                @if ($errors->any())
                <div class="err">{{ $errors->first() }}</div>
                @endif

                <button class="btn block" type="submit">パスワードを再設定する</button>
            </form>
        </div>
    </div>

    @vite(['resources/js/app.js'])

</body>

</html>
