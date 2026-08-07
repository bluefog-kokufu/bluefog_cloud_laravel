<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function get(): Company
    {
        return Company::query()->firstOrCreate([], [
            'name' => 'ユーザー企業株式会社',
            'tax_rate' => 10,
            'rounding' => 'floor',
        ]);
    }

    public function update(Company $company, array $data): Company
    {
        $company->update($data);

        return $company;
    }
}
