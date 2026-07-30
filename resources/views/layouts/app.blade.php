<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <span id="userLabel"></span>
                <button class="btn ghost small" onclick="logout()">ログアウト</button>
            </div>
        </header>
        <div class="layout">
            <nav class="side" id="sideNav">
                <div class="clock">
                    <div class="t" id="clockTime">--:--:--</div>
                    <div class="d" id="clockDate"></div>
                </div>
                <div class="navttl">MENU</div>
                <a data-page="home">ホーム</a>
                <a data-page="customers">顧客管理</a>
                <a data-page="sales">受注取引一覧</a>
                <a data-page="purchases">発注取引一覧(アップロード)</a>
                <div class="navttl">支払通知書管理</div>
                <a data-page="paynotices">支払通知書一覧</a>
                <a data-page="payform">支払通知書作成</a>
                <div class="navttl">会計帳簿</div>
                <a data-page="ledger">総勘定元帳</a>
                <a data-page="bs">貸借対照表</a>
                <a data-page="pl">損益計算書</a>
                <a data-page="cf">キャッシュフロー計算書</a>
                <div class="navttl">賃貸革命連携</div>
                <a data-page="m_landlords">家主基本情報</a>
                <a data-page="m_contractors">契約者情報</a>
                <a data-page="m_repairers">修繕業者情報</a>
                <a data-page="m_agents">仲介・管理業者情報</a>
                <a data-page="m_insurers">保険会社情報</a>
                <div class="navttl">設定</div>
                <a data-page="settings">会計・消費税設定</a>
                <a data-page="profile">プロフィール</a>
            </nav>
            <main id="page">
                @yield('content')
            </main>
        </div>
    </div>

    @vite(['resources/js/app.js'])

</body>

</html>