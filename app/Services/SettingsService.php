<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\CompanyRepositoryInterface;

class SettingsService
{
    /** 消費税率の選択肢 */
    public const TAX_RATES = [10, 8, 0];

    /** 端数処理の選択肢 */
    public const ROUNDING_OPTIONS = [
        'floor' => '切り捨て',
        'round' => '四捨五入',
        'ceil' => '切り上げ',
    ];

    public function __construct(private readonly CompanyRepositoryInterface $companies) {}

    public function get(): Company
    {
        return $this->companies->get();
    }

    public function update(Company $company, array $data): Company
    {
        return $this->companies->update($company, $data);
    }
}
