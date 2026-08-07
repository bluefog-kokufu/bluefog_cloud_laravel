<?php

namespace App\Repositories;

use App\Models\BalanceSheet;

class BalanceSheetRepository implements BalanceSheetRepositoryInterface
{
    public function get(): BalanceSheet
    {
        return BalanceSheet::query()->firstOrCreate([], [
            'date' => now()->format('Y-m-d'),
            'assets' => [],
            'liabs' => [],
            'equity' => [],
        ]);
    }

    public function update(BalanceSheet $balanceSheet, array $data): BalanceSheet
    {
        $balanceSheet->update($data);

        return $balanceSheet;
    }
}
