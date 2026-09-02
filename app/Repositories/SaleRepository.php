<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Support\Pagination;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SaleRepository implements SaleRepositoryInterface
{
    public function paginate(array $filters, int $perPage = Pagination::PER_PAGE): LengthAwarePaginator
    {
        $query = $filters['q'] ?? null;
        $method = $filters['method'] ?? null;
        $status = $filters['status'] ?? null;
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        return Sale::query()
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

    public function allWithItems(): Collection
    {
        return Sale::query()->with(['customer', 'items'])->orderByDesc('date')->get();
    }

    public function findWithItems(string $id): ?Sale
    {
        return Sale::query()->with('items')->find($id);
    }

    public function create(array $data, array $items): Sale
    {
        $sale = Sale::create($data);
        $sale->items()->createMany($items);

        return $sale->load('items');
    }

    public function update(Sale $sale, array $data, array $items): Sale
    {
        $sale->update($data);
        $sale->items()->delete();
        $sale->items()->createMany($items);

        return $sale->load('items');
    }

    public function delete(Sale $sale): void
    {
        $sale->delete();
    }

    public function countByInvoiceNoPrefix(string $prefix): int
    {
        return Sale::query()->where('invoice_no', 'like', $prefix.'%')->count();
    }
}
