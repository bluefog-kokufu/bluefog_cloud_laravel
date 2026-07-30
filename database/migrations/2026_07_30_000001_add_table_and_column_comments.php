<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $tableComments = [
            'users' => 'ログイン画面・ダッシュボードで利用する認証ユーザー情報',
            'companies' => '会計・消費税設定画面で利用する会社情報',
            'customers' => '顧客管理画面で顧客一覧・登録・請求書作成に利用する顧客情報',
            'sales' => '受注取引一覧・取引作成・請求書作成画面で利用する売上取引',
            'sale_items' => '受注取引・請求書作成画面の売上明細行',
            'purchases' => '発注取引一覧(アップロード)・書類アップロード画面で利用する仕入取引',
            'ledger_entries' => '総勘定元帳画面で利用する仕訳データ',
            'payment_notices' => '支払通知書一覧・作成画面で利用する支払通知書',
            'm_landlords' => '賃貸革命連携画面で利用する家主基本情報',
            'm_contractors' => '賃貸革命連携画面で利用する契約者情報',
            'm_repairers' => '賃貸革命連携画面で利用する修繕業者情報',
            'm_agents' => '賃貸革命連携画面で利用する仲介・管理業者情報',
            'm_insurers' => '賃貸革命連携画面で利用する保険会社情報',
        ];

        foreach ($tableComments as $table => $comment) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` COMMENT = '" . str_replace("'", "''", $comment) . "'");
            }
        }

        $columnComments = [
            'users' => [
                'id' => 'ユーザーID（ログイン認証用）',
                'name' => 'ログイン後の画面表示名',
                'email' => 'ログイン画面で利用するメールアドレス',
                'email_verified_at' => 'メール認証日時',
                'password' => 'ログイン認証用パスワード',
                'remember_token' => 'ログイン状態保持用トークン',
            ],
            'companies' => [
                'id' => '会社ID',
                'name' => '会社名（設定画面・請求書に表示）',
                'reg_no' => '適格請求書発行事業者登録番号',
                'zip' => '会社郵便番号',
                'addr' => '会社住所',
                'tel' => '会社電話番号',
                'bank' => '振込先銀行口座情報',
            ],
            'customers' => [
                'id' => '顧客ID',
                'name' => '会社名（顧客一覧・請求書に表示）',
                'person' => '担当者名',
                'email' => '顧客連絡先メールアドレス',
                'tel' => '顧客電話番号',
                'addr' => '顧客住所',
                'site' => '支払サイト情報',
                'reg_no' => '適格請求書発行事業者登録番号',
                'memo' => '顧客メモ',
            ],
            'sales' => [
                'id' => '売上取引ID',
                'date' => '取引年月日',
                'cust_id' => '顧客ID',
                'method' => '入金方法',
                'amount' => '税抜金額',
                'tax' => '税額',
                'status' => '請求・入金ステータス',
                'invoiced' => '請求書発行日時',
                'memo' => '備考',
            ],
            'sale_items' => [
                'id' => '明細ID',
                'sale_id' => '売上取引ID',
                'name' => '品目・内容',
                'amount' => '税抜金額',
                'rate' => '消費税率',
            ],
            'purchases' => [
                'id' => '仕入取引ID',
                'date' => '取引年月日',
                'cust_id' => '顧客ID',
                'method' => '支払方法',
                'amount' => '税抜金額',
                'tax' => '税額',
                'status' => '支払ステータス',
                'files' => 'アップロード済み書類情報',
                'memo' => 'メモ',
                'up' => 'アップロード日',
            ],
            'ledger_entries' => [
                'id' => '仕訳ID',
                'no' => '伝票No.',
                'm' => '月',
                'd' => '日',
                'acct' => '勘定科目',
                'note' => '摘要',
                'page' => '仕丁',
                'dr' => '借方金額',
                'cr' => '貸方金額',
            ],
            'payment_notices' => [
                'id' => '支払通知書ID',
                'cust_id' => '顧客ID',
                'title' => '通知書タイトル',
                'pay_date' => '支払期日',
                'items' => '通知書明細',
            ],
            'm_landlords' => [
                'id' => '家主ID',
                'name' => '家主名',
                'contact' => '担当者名',
                'email' => '連絡先メールアドレス',
                'tel' => '電話番号',
                'addr' => '住所',
                'memo' => 'メモ',
            ],
            'm_contractors' => [
                'id' => '契約者ID',
                'name' => '契約者名',
                'contact' => '担当者名',
                'email' => '連絡先メールアドレス',
                'tel' => '電話番号',
                'addr' => '住所',
                'memo' => 'メモ',
            ],
            'm_repairers' => [
                'id' => '修繕業者ID',
                'name' => '業者名',
                'contact' => '担当者名',
                'email' => '連絡先メールアドレス',
                'tel' => '電話番号',
                'addr' => '住所',
                'memo' => 'メモ',
            ],
            'm_agents' => [
                'id' => '業者ID',
                'name' => '業者名',
                'contact' => '担当者名',
                'email' => '連絡先メールアドレス',
                'tel' => '電話番号',
                'addr' => '住所',
                'memo' => 'メモ',
            ],
            'm_insurers' => [
                'id' => '保険会社ID',
                'name' => '会社名',
                'contact' => '担当者名',
                'email' => '連絡先メールアドレス',
                'tel' => '電話番号',
                'addr' => '住所',
                'memo' => 'メモ',
            ],
        ];

        foreach ($columnComments as $table => $cols) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            foreach ($cols as $column => $comment) {
                if (DB::getSchemaBuilder()->hasColumn($table, $column)) {
                    $columns = DB::select("SHOW FULL COLUMNS FROM `{$table}`");
                    $columnInfo = collect($columns)->firstWhere('Field', $column);

                    if (! $columnInfo) {
                        continue;
                    }

                    $nullClause = strtoupper($columnInfo->Null) === 'YES' ? 'NULL' : 'NOT NULL';
                    $columnType = $columnInfo->Type;
                    $autoIncrement = stripos($columnInfo->Extra, 'auto_increment') !== false ? ' AUTO_INCREMENT' : '';
                    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` {$columnType} {$nullClause}{$autoIncrement} COMMENT '" . str_replace("'", "''", $comment) . "'");
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // コメントはロールバックしません。
    }
};
