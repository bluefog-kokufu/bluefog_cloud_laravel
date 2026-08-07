<?php

namespace App\Services;

use App\Models\CashFlowStatement;
use App\Repositories\CashFlowStatementRepositoryInterface;

class CashFlowStatementService
{
    public function __construct(private readonly CashFlowStatementRepositoryInterface $cashFlowStatements) {}

    public function get(): CashFlowStatement
    {
        return $this->cashFlowStatements->get();
    }

    public function update(CashFlowStatement $cashFlowStatement, array $data): CashFlowStatement
    {
        return $this->cashFlowStatements->update($cashFlowStatement, $data);
    }

    /**
     * 営業・投資・財務それぞれのキャッシュ・フローと期末残高を算出する
     */
    public function totals(CashFlowStatement $cashFlowStatement): array
    {
        $operating = $this->sumRows($cashFlowStatement->operating ?? []);
        $investing = $this->sumRows($cashFlowStatement->investing ?? []);
        $financing = $this->sumRows($cashFlowStatement->financing ?? []);
        $delta = $operating + $investing + $financing;
        $beginningBalance = (int) $cashFlowStatement->beginning_balance;

        return [
            'operating' => $operating,
            'investing' => $investing,
            'financing' => $financing,
            'delta' => $delta,
            'endingBalance' => $beginningBalance + $delta,
        ];
    }

    public function sumRows(array $rows): int
    {
        return array_sum(array_map(fn ($row) => (int) ($row['v'] ?? 0), $rows));
    }

    public function exportRows(CashFlowStatement $cashFlowStatement): array
    {
        $totals = $this->totals($cashFlowStatement);

        $rows = [
            ['キャッシュフロー計算書', '自', optional($cashFlowStatement->period_from)->format('Y-m-d'), '至', optional($cashFlowStatement->period_to)->format('Y-m-d')],
            ['Ⅰ 営業活動によるキャッシュ・フロー', ''],
        ];
        foreach ($cashFlowStatement->operating ?? [] as $row) {
            $rows[] = [$row['name'] ?? '', $row['v'] ?? 0];
        }
        $rows[] = ['営業活動によるキャッシュ・フロー', $totals['operating']];
        $rows[] = ['Ⅱ 投資活動によるキャッシュ・フロー', ''];
        foreach ($cashFlowStatement->investing ?? [] as $row) {
            $rows[] = [$row['name'] ?? '', $row['v'] ?? 0];
        }
        $rows[] = ['投資活動によるキャッシュ・フロー', $totals['investing']];
        $rows[] = ['Ⅲ 財務活動によるキャッシュ・フロー', ''];
        foreach ($cashFlowStatement->financing ?? [] as $row) {
            $rows[] = [$row['name'] ?? '', $row['v'] ?? 0];
        }
        $rows[] = ['財務活動によるキャッシュ・フロー', $totals['financing']];
        $rows[] = ['Ⅳ 現金及び現金同等物の増減額', $totals['delta']];
        $rows[] = ['Ⅴ 現金及び現金同等物の期首残高', $cashFlowStatement->beginning_balance];
        $rows[] = ['Ⅵ 現金及び現金同等物の期末残高', $totals['endingBalance']];

        return $rows;
    }
}
