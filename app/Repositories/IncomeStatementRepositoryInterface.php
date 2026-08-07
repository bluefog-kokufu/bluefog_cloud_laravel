<?php

namespace App\Repositories;

use App\Models\IncomeStatement;

interface IncomeStatementRepositoryInterface
{
    /**
     * 損益計算書を取得する（未作成の場合は初期値で新規作成する）
     */
    public function get(): IncomeStatement;

    /**
     * 損益計算書を更新する
     */
    public function update(IncomeStatement $incomeStatement, array $data): IncomeStatement;
}
