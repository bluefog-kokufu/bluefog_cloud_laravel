<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface LedgerEntryRepositoryInterface
{
    /**
     * 全ての仕訳行を登録順に取得する
     */
    public function all(): Collection;

    /**
     * 仕訳帳の内容を丸ごと置き換える（保存ボタン押下時に利用）
     */
    public function replaceAll(array $rows): void;

    /**
     * 仕訳行を追加登録する（CSVインポート時に利用）
     */
    public function createMany(array $rows): int;
}
