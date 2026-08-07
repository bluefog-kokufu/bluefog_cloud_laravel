<?php

namespace App\Services;

use App\Models\BalanceSheet;
use App\Repositories\BalanceSheetRepositoryInterface;

class BalanceSheetService
{
    public function __construct(private readonly BalanceSheetRepositoryInterface $balanceSheets) {}

    public function get(): BalanceSheet
    {
        return $this->balanceSheets->get();
    }

    public function update(BalanceSheet $balanceSheet, array $data): BalanceSheet
    {
        return $this->balanceSheets->update($balanceSheet, $data);
    }

    /**
     * 資産・負債・純資産それぞれの合計と貸借一致を算出する
     */
    public function totals(BalanceSheet $balanceSheet): array
    {
        $assets = $this->sumRows($balanceSheet->assets ?? []);
        $liabs = $this->sumRows($balanceSheet->liabs ?? []);
        $equity = $this->sumRows($balanceSheet->equity ?? []);

        return [
            'assets' => $assets,
            'liabs' => $liabs,
            'equity' => $equity,
            'liabsAndEquity' => $liabs + $equity,
            'balanced' => $assets === $liabs + $equity,
        ];
    }

    public function sumRows(array $rows): int
    {
        return array_sum(array_map(fn ($row) => (int) ($row['v'] ?? 0), $rows));
    }

    public function exportRows(BalanceSheet $balanceSheet): array
    {
        $totals = $this->totals($balanceSheet);

        $rows = [
            ['貸借対照表', '日付', optional($balanceSheet->date)->format('Y-m-d')],
            ['【資産の部】', ''],
        ];
        foreach ($balanceSheet->assets ?? [] as $row) {
            $rows[] = [$row['name'] ?? '', $row['v'] ?? 0];
        }
        $rows[] = ['資産の部合計', $totals['assets']];
        $rows[] = ['【負債の部】', ''];
        foreach ($balanceSheet->liabs ?? [] as $row) {
            $rows[] = [$row['name'] ?? '', $row['v'] ?? 0];
        }
        $rows[] = ['負債の部合計', $totals['liabs']];
        $rows[] = ['【純資産の部】', ''];
        foreach ($balanceSheet->equity ?? [] as $row) {
            $rows[] = [$row['name'] ?? '', $row['v'] ?? 0];
        }
        $rows[] = ['純資産の部合計', $totals['equity']];
        $rows[] = ['負債・純資産の部合計', $totals['liabsAndEquity']];

        return $rows;
    }
}
