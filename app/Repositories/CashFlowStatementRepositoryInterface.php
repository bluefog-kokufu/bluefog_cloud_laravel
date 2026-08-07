<?php

namespace App\Repositories;

use App\Models\CashFlowStatement;

interface CashFlowStatementRepositoryInterface
{
    /**
     * キャッシュフロー計算書を取得する（未作成の場合は初期値で新規作成する）
     */
    public function get(): CashFlowStatement;

    /**
     * キャッシュフロー計算書を更新する
     */
    public function update(CashFlowStatement $cashFlowStatement, array $data): CashFlowStatement;
}
