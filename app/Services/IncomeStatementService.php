<?php

namespace App\Services;

use App\Models\IncomeStatement;
use App\Repositories\IncomeStatementRepositoryInterface;

class IncomeStatementService
{
    public const TYPES = ['収益', '費用'];

    public function __construct(private readonly IncomeStatementRepositoryInterface $incomeStatements) {}

    public function get(): IncomeStatement
    {
        return $this->incomeStatements->get();
    }

    public function update(IncomeStatement $incomeStatement, array $data): IncomeStatement
    {
        return $this->incomeStatements->update($incomeStatement, $data);
    }

    /**
     * 収益合計・費用合計・当期純利益を算出する
     */
    public function totals(IncomeStatement $incomeStatement): array
    {
        $rows = $incomeStatement->rows ?? [];
        $revenueRows = array_filter($rows, fn ($row) => ($row['type'] ?? '') === '収益');
        $expenseRows = array_filter($rows, fn ($row) => ($row['type'] ?? '') === '費用');
        $revenue = $this->sumRows($revenueRows);
        $expense = $this->sumRows($expenseRows);

        return [
            'revenue' => $revenue,
            'expense' => $expense,
            'profit' => $revenue - $expense,
        ];
    }

    public function sumRows(array $rows): int
    {
        return array_sum(array_map(fn ($row) => (int) ($row['v'] ?? 0), $rows));
    }

    public function exportRows(IncomeStatement $incomeStatement): array
    {
        $totals = $this->totals($incomeStatement);

        $rows = [
            ['損益計算書', '自', optional($incomeStatement->period_from)->format('Y-m-d'), '至', optional($incomeStatement->period_to)->format('Y-m-d')],
            ['科目', '区分', '金額'],
        ];
        foreach ($incomeStatement->rows ?? [] as $row) {
            $rows[] = [$row['name'] ?? '', $row['type'] ?? '', $row['v'] ?? 0];
        }
        $rows[] = ['収益合計', '', $totals['revenue']];
        $rows[] = ['費用合計', '', $totals['expense']];
        $rows[] = ['当期純利益', '', $totals['profit']];

        return $rows;
    }
}
