<?php

namespace App\Services;

use App\Models\PaymentNotice;
use App\Repositories\PaymentNoticeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PaymentNoticeService
{
    /** 消費税区分の選択肢 */
    public const TAX_OPTIONS = ['非課税', '8%', '8%軽減税率', '10%'];

    public function __construct(private readonly PaymentNoticeRepositoryInterface $paymentNotices) {}

    /**
     * 一覧を取得し、明細から算出した合計金額を各行に付与する
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $paginator = $this->paymentNotices->paginate($filters);
        $paginator->getCollection()->transform(function (PaymentNotice $notice) {
            $notice->setAttribute('totals', $this->totals($notice->items ?? []));

            return $notice;
        });

        return $paginator;
    }

    public function find(string $id): ?PaymentNotice
    {
        return $this->paymentNotices->find($id);
    }

    /**
     * 支払通知書を明細ごと作成する。通知番号は自動採番する。
     */
    public function create(array $data): PaymentNotice
    {
        return DB::transaction(function () use ($data) {
            $data['id'] = $this->generateNoticeNo();

            return $this->paymentNotices->create($data);
        });
    }

    public function update(PaymentNotice $paymentNotice, array $data): PaymentNotice
    {
        return $this->paymentNotices->update($paymentNotice, $data);
    }

    public function delete(PaymentNotice $paymentNotice): void
    {
        $this->paymentNotices->delete($paymentNotice);
    }

    /**
     * 明細から小計・消費税・合計を算出する（明細行ごとに端数処理する）
     */
    public function totals(array $items): array
    {
        $sub = 0;
        $tax = 0;
        foreach ($items as $item) {
            $amount = (int) ($item['price'] ?? 0) * (int) ($item['qty'] ?? 0);
            $sub += $amount;
            $tax += $this->calcTaxAt($amount, $this->taxRate($item['tax'] ?? ''));
        }

        return ['sub' => $sub, 'tax' => $tax, 'total' => $sub + $tax];
    }

    /**
     * 消費税区分から税率(%)を算出する
     */
    public function taxRate(string $tax): int
    {
        return match ($tax) {
            '10%' => 10,
            '8%', '8%軽減税率' => 8,
            default => 0,
        };
    }

    private function calcTaxAt(int $amount, int $rate): int
    {
        return (int) floor($amount * $rate / 100);
    }

    /**
     * 通知番号を発行する（形式: SC-YYYYMMDD-001。同日中の登録件数から連番を決定する）
     */
    private function generateNoticeNo(): string
    {
        $date = now()->format('Ymd');
        $seq = $this->paymentNotices->countByIdPrefix("SC-{$date}-") + 1;

        return sprintf('SC-%s-%03d', $date, $seq);
    }
}
