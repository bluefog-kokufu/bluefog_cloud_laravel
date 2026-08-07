<?php

namespace App\Repositories;

use App\Models\LedgerEntry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LedgerEntryRepository implements LedgerEntryRepositoryInterface
{
    public function all(): Collection
    {
        return LedgerEntry::query()->orderBy('id')->get();
    }

    public function replaceAll(array $rows): void
    {
        DB::transaction(function () use ($rows) {
            LedgerEntry::query()->delete();
            foreach ($rows as $row) {
                LedgerEntry::create($row);
            }
        });
    }

    public function createMany(array $rows): int
    {
        foreach ($rows as $row) {
            LedgerEntry::create($row);
        }

        return count($rows);
    }
}
