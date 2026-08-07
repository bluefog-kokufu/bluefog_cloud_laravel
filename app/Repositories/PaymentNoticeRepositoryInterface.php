<?php

namespace App\Repositories;

use App\Models\PaymentNotice;
use Illuminate\Pagination\LengthAwarePaginator;

interface PaymentNoticeRepositoryInterface
{
    /**
     * 絞り込み条件付きで支払通知書を取得する
     */
    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator;

    /**
     * 取引先付きで1件の支払通知書を取得する
     */
    public function find(string $id): ?PaymentNotice;

    /**
     * 支払通知書を新規作成する
     */
    public function create(array $data): PaymentNotice;

    /**
     * 支払通知書を更新する
     */
    public function update(PaymentNotice $paymentNotice, array $data): PaymentNotice;

    /**
     * 支払通知書を削除する
     */
    public function delete(PaymentNotice $paymentNotice): void;

    /**
     * 指定した通知番号の接頭辞に一致する件数を取得する（採番に利用）
     */
    public function countByIdPrefix(string $prefix): int;
}
