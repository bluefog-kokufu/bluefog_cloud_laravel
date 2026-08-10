<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Sale;

class DashboardService
{
    /**
     * マイページトップの集計値（登録顧客数・今月の売上・未回収売掛金・未払買掛金）を取得する
     */
    public function summary(): array
    {
        return [
            'customerCount' => Customer::query()->count(),
            'monthlySales' => $this->monthlySales(),
            'unpaidReceivables' => $this->unpaidReceivables(),
            'unpaidPayables' => $this->unpaidPayables(),
        ];
    }

    /**
     * 今月分の売上取引の税抜金額合計
     */
    private function monthlySales(): int
    {
        return (int) Sale::query()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('amount');
    }

    /**
     * 入金済以外の売上取引の税込金額合計（未回収売掛金）
     */
    private function unpaidReceivables(): int
    {
        return (int) Sale::query()
            ->where('status', '!=', '入金済')
            ->selectRaw('COALESCE(SUM(amount + tax), 0) as total')
            ->value('total');
    }

    /**
     * 支払い済以外の仕入取引の税込金額合計（未払買掛金）
     */
    private function unpaidPayables(): int
    {
        return (int) Purchase::query()
            ->where('status', '!=', '支払い済')
            ->selectRaw('COALESCE(SUM(amount + tax), 0) as total')
            ->value('total');
    }
}
