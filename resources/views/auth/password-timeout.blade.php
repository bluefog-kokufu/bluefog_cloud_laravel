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
            <div class="sub">{{ $expired ? 'タイムアウトしました' : 'URLが無効です' }}</div>

            <div class="err" style="color:var(--muted)">
                @if ($expired)
                パスワード再設定用URLの有効期限が切れています。お手数ですが、再度パスワード再発行の手続きを行ってください。
                @else
                URLが正しくないか、既に使用済みです。お手数ですが、再度パスワード再発行の手続きを行ってください。
                @endif
            </div>

            <a class="btn block" style="text-align:center;text-decoration:none;box-sizing:border-box" href="{{ route('password.forgot') }}">パスワード再発行へ</a>
        </div>
    </div>

    @vite(['resources/js/app.js'])

</body>

</html>
