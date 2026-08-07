<?php

namespace App\Repositories;

use App\Models\IncomeStatement;

class IncomeStatementRepository implements IncomeStatementRepositoryInterface
{
    public function get(): IncomeStatement
    {
        return IncomeStatement::query()->firstOrCreate([], [
            'period_from' => now()->startOfYear()->format('Y-m-d'),
            'period_to' => now()->endOfYear()->format('Y-m-d'),
            'rows' => [],
        ]);
    }

    public function update(IncomeStatement $incomeStatement, array $data): IncomeStatement
    {
        $incomeStatement->update($data);

        return $incomeStatement;
    }
}
