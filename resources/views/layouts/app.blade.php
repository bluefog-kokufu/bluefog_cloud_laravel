<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bluefog Cloud</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    <!-- ================= APP ================= -->
    <div id="app" style="display:block;">
        <header class="topbar">
            <div class="brand"><span class="mark"></span>Bluefog Cloud</div>
            <div class="userbox">
                <a class="manual-link" href="manual.html" target="_blank" title="ユーザー利用マニュアルを開く">📖 操作マニュアル</a>
                <span id="companyName">{{ \App\Models\Company::query()->first()?->name }}</span>
                <span id="userLabel"></span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn ghost small">ログアウト</button>
                </form>
            </div>
        </header>
        <div class="layout">
            <nav class="side" id="sideNav">
                <div class="clock">
                    <div class="t" id="clockTime">--:--:--</div>
                    <div class="d" id="clockDate"></div>
                </div>
                <div class="navttl">MENU</div>
                <a data-page="home" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">ホーム</a>
                <a data-page="customers" href="{{ route('customer') }}" class="{{ request()->routeIs('customer') ? 'active' : '' }}">顧客管理</a>
                <a data-page="sales" href="{{ route('sale') }}" class="{{ request()->routeIs('sale') ? 'active' : '' }}">受注取引一覧</a>
                <a data-page="purchases" href="{{ route('purchase') }}" class="{{ request()->routeIs('purchase') ? 'active' : '' }}">発注取引一覧(アップロード)</a>
                <div class="navttl">支払通知書管理</div>
                <a data-page="paynotices" href="{{ route('paynotice') }}" class="{{ request()->routeIs('paynotice') ? 'active' : '' }}">支払通知書一覧</a>
                <a data-page="payform" href="{{ route('paynotice.create') }}" class="{{ request()->routeIs('paynotice.create') ? 'active' : '' }}">支払通知書作成</a>
                <div class="navttl">会計帳簿</div>
                <a data-page="ledger" href="{{ route('ledger') }}" class="{{ request()->routeIs('ledger') ? 'active' : '' }}">総勘定元帳</a>
                <a data-page="bs" href="{{ route('bs') }}" class="{{ request()->routeIs('bs') ? 'active' : '' }}">貸借対照表</a>
                <a data-page="pl" href="{{ route('pl') }}" class="{{ request()->routeIs('pl') ? 'active' : '' }}">損益計算書</a>
                <a data-page="cf" href="{{ route('cf') }}" class="{{ request()->routeIs('cf') ? 'active' : '' }}">キャッシュフロー計算書</a>
                <div class="navttl">設定</div>
                <a data-page="settings" href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'active' : '' }}">会計・消費税設定</a>
                <a data-page="profile" class="{{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">プロフィール</a>
            </nav>
            <main id="page">
                @yield('content')
            </main>
        </div>
    </div>

    <div class="modal-bg" id="modalBg">
        <div class="modal" id="modalBox"></div>
    </div>

    <div id="invoicePrintArea"></div>

    @vite(['resources/js/app.js'])

</body>

</html>