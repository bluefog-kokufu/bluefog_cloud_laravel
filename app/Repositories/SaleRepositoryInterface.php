<?php

namespace App\Repositories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SaleRepositoryInterface
{
    /**
     * 絞り込み条件付きで受注取引を取得する
     */
    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator;

    /**
     * CSVエクスポート用に全ての受注取引を明細付きで取得する
     */
    public function allWithItems(): Collection;

    /**
     * 明細・顧客付きで1件の受注取引を取得する
     */
    public function findWithItems(string $id): ?Sale;

    /**
     * 受注取引を明細ごと新規作成する
     */
    public function create(array $data, array $items): Sale;

    /**
     * 受注取引を明細ごと更新する
     */
    public function update(Sale $sale, array $data, array $items): Sale;

    /**
     * 受注取引を削除する
     */
    public function delete(Sale $sale): void;

    /**
     * 指定した請求書番号の接頭辞に一致する件数を取得する（採番に利用）
     */
    public function countByInvoiceNoPrefix(string $prefix): int;
}
