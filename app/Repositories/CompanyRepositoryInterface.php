<?php

namespace App\Repositories;

use App\Models\Company;

interface CompanyRepositoryInterface
{
    /**
     * 自社情報を取得する（未作成の場合は初期値で新規作成する）
     */
    public function get(): Company;

    /**
     * 自社情報を更新する
     */
    public function update(Company $company, array $data): Company;
}
