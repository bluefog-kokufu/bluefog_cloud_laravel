<?php

namespace App\Repositories;

use App\Models\Purchase;
use App\Support\Pagination;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PurchaseRepositoryInterface
{
    /**
     * 絞り込み条件付きで発注取引(仕入)を取得する
     */
    public function paginate(array $filters, int $perPage = Pagination::PER_PAGE): LengthAwarePaginator;

    /**
     * CSVエクスポート用に全ての発注取引を取得する
     */
    public function all(): Collection;

    /**
     * 1件の発注取引を取得する
     */
    public function find(string $id): ?Purchase;

    /**
     * 発注取引を新規作成する
     */
    public function create(array $data): Purchase;

    /**
     * 発注取引を更新する
     */
    public function update(Purchase $purchase, array $data): Purchase;

    /**
     * 発注取引を削除する
     */
    public function delete(Purchase $purchase): void;
}
