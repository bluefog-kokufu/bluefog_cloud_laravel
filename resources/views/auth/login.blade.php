<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - Bluefog Cloud</title>
    <style>
        body{font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif;background:#f5f7fb;color:#1f2937;min-height:100vh;display:flex;align-items:center;justify-content:center;margin:0}
        .card{width:100%;max-width:420px;padding:32px;background:#fff;border:1px solid #d2d6dc;border-radius:16px;box-shadow:0 16px 40px rgba(15,23,42,.08)}
        .card h1{margin:0 0 16px;font-size:24px;color:#0f172a}
        .field{margin-bottom:16px}
        .field label{display:block;margin-bottom:6px;font-size:13px;color:#475569}
        .field input{width:100%;padding:12px 14px;border:1px solid #cbd5e1;border-radius:12px;background:#f8fafc;color:#0f172a;font-size:14px}
        .field input:focus{outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.12)}
        .error{margin-bottom:16px;padding:12px 14px;background:#fee2e2;color:#991b1b;border:1px solid #fecaca;border-radius:12px;font-size:14px}
        .actions{display:flex;justify-content:space-between;align-items:center;gap:12px}
        .btn{width:100%;padding:12px 14px;font-size:15px;font-weight:700;border:none;border-radius:12px;background:#0f172a;color:#fff;cursor:pointer}
        .btn:hover{background:#334155}
        .link{font-size:13px;color:#475569;text-decoration:none}
    </style>
</head>
<body>
    <div class="card">
        <h1>Bluefog Cloud ログイン</h1>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="field">
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>

            <div class="field">
                <label for="password">パスワード</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>

            <div class="field">
                <label><input type="checkbox" name="remember"> ログイン状態を保持する</label>
            </div>

            <button type="submit" class="btn">ログイン</button>
        </form>

        <p style="margin-top:18px;font-size:13px;color:#64748b">サンプルアカウント: user@user.com / password</p>
    </div>
</body>
</html>
