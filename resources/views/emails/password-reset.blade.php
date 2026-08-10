<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
</head>

<body>
    <p>Bluefog Cloud をご利用いただきありがとうございます。</p>
    <p>パスワード再設定のリクエストを受け付けました。以下のURLから再設定を行ってください。</p>
    <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
    <p>このURLの有効期限は発行から{{ \App\Services\PasswordResetService::EXPIRY_MINUTES }}分です。</p>
    <p>心当たりがない場合は、このメールを破棄してください。</p>
</body>

</html>
