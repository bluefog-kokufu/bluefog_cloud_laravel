<?php

namespace App\Repositories;

use App\Models\CashFlowStatement;

class CashFlowStatementRepository implements CashFlowStatementRepositoryInterface
{
    public function get(): CashFlowStatement
    {
        return CashFlowStatement::query()->firstOrCreate([], [
            'period_from' => now()->startOfYear()->format('Y-m-d'),
            'period_to' => now()->endOfYear()->format('Y-m-d'),
            'beginning_balance' => 0,
            'operating' => [],
            'investing' => [],
            'financing' => [],
        ]);
    }

    public function update(CashFlowStatement $cashFlowStatement, array $data): CashFlowStatement
    {
        $cashFlowStatement->update($data);

        return $cashFlowStatement;
    }
}
