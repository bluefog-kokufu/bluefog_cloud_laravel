<?php

namespace App\Repositories;

use App\Models\PaymentNotice;
use App\Support\Pagination;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentNoticeRepository implements PaymentNoticeRepositoryInterface
{
    public function paginate(array $filters, int $perPage = Pagination::PER_PAGE): LengthAwarePaginator
    {
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        return PaymentNotice::query()
            ->with('customer')
            ->when($from, fn ($builder, $from) => $builder->whereDate('pay_date', '>=', $from))
            ->when($to, fn ($builder, $to) => $builder->whereDate('pay_date', '<=', $to))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(string $id): ?PaymentNotice
    {
        return PaymentNotice::query()->with('customer')->find($id);
    }

    public function create(array $data): PaymentNotice
    {
        return PaymentNotice::create($data);
    }

    public function update(PaymentNotice $paymentNotice, array $data): PaymentNotice
    {
        $paymentNotice->update($data);

        return $paymentNotice;
    }

    public function delete(PaymentNotice $paymentNotice): void
    {
        $paymentNotice->delete();
    }

    public function countByIdPrefix(string $prefix): int
    {
        return PaymentNotice::query()->where('id', 'like', $prefix.'%')->count();
    }
}
