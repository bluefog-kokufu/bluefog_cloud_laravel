<?php

namespace App\Repositories;

use App\Models\BalanceSheet;

interface BalanceSheetRepositoryInterface
{
    /**
     * 貸借対照表を取得する（未作成の場合は初期値で新規作成する）
     */
    public function get(): BalanceSheet;

    /**
     * 貸借対照表を更新する
     */
    public function update(BalanceSheet $balanceSheet, array $data): BalanceSheet;
}
