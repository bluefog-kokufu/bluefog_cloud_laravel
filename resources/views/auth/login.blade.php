<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bluefog Cloud</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <!-- ================= LOGIN ================= -->
    <div id="loginView">
        <div class="login-card">
            <h1>Bluefog Cloud</h1>
            <div class="sub">ログイン画面</div>

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <label for="email">メールアドレス</label>
                <input id="loginEmail" type="email" name="email" value="{{ old('email') }}" placeholder="user@user.com" autofocus autocomplete="username">

                <label for="password">パスワード</label>
                <input id="loginPw" type="password" name="password" placeholder="パスワード" autocomplete="current-password">

                @if ($errors->any())
                <div class="err" id="loginErr">{{ $errors->first() }}</div>
                @endif
                @if (session('status'))
                <div class="err" style="color:var(--navy)">{{ session('status') }}</div>
                @endif
                <!--
                <div class="field">
                    <label><input type="checkbox" name="remember"> ログイン状態を保持する</label>
                </div>
                -->

                <button class="btn block" type="submit">ログイン</button>
                <div class="login-links">
                    <a href="{{ route('password.forgot') }}">パスワードを忘れた方</a>
                </div>
            </form>

        </div>
    </div>

    @vite(['resources/js/app.js'])

</body>

</html>