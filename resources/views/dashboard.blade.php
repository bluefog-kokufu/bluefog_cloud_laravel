<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ダッシュボード - Bluefog Cloud</title>
    <style>
        body{font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif;background:#eef2ff;color:#0f172a;min-height:100vh;display:flex;align-items:center;justify-content:center;margin:0}
        .panel{width:100%;max-width:720px;padding:32px;background:#fff;border:1px solid #c7d2fe;border-radius:16px;box-shadow:0 18px 50px rgba(99,102,241,.12)}
        h1{margin-top:0;font-size:28px;color:#312e81}
        p{margin:0 0 16px;color:#475569}
        form{display:inline}
        .btn{padding:12px 18px;border:none;border-radius:12px;background:#4338ca;color:#fff;font-weight:700;cursor:pointer}
    </style>
</head>
<body>
    <div class="panel">
        <h1>Bluefog Cloud ダッシュボード</h1>
        <p>ログインに成功しました。ここから業務系システムの画面に移動できます。</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn">ログアウト</button>
        </form>
    </div>
</body>
</html>
