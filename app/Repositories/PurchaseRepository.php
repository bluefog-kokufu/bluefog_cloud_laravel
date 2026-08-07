<?php

namespace App\Repositories;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseRepository implements PurchaseRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $filters['q'] ?? null;
        $method = $filters['method'] ?? null;
        $status = $filters['status'] ?? null;
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        return Purchase::query()
            ->with('customer')
            ->when($query, function ($builder, $query) {
                $builder->where(function ($sub) use ($query) {
                    $sub->where('id', 'like', "%{$query}%")
                        ->orWhere('memo', 'like', "%{$query}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($query) {
                            $customerQuery->where('name', 'like', "%{$query}%");
                        });
                });
            })
            ->when($method, fn ($builder, $method) => $builder->where('method', $method))
            ->when($status, fn ($builder, $status) => $builder->where('status', $status))
            ->when($from, fn ($builder, $from) => $builder->whereDate('date', '>=', $from))
            ->when($to, fn ($builder, $to) => $builder->whereDate('date', '<=', $to))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function all(): Collection
    {
        return Purchase::query()->with('customer')->orderByDesc('date')->get();
    }

    public function find(string $id): ?Purchase
    {
        return Purchase::query()->with('customer')->find($id);
    }

    public function create(array $data): Purchase
    {
        return Purchase::create($data);
    }

    public function update(Purchase $purchase, array $data): Purchase
    {
        $purchase->update($data);

        return $purchase;
    }

    public function delete(Purchase $purchase): void
    {
        $purchase->delete();
    }
}
