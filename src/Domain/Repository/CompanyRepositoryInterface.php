<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Adapter\Framework\Http\API\Filter\CompanyFilter;
use App\Adapter\Framework\Http\API\Response\PaginatedResponse;
use App\Domain\Model\Company;

interface CompanyRepositoryInterface
{
    public function add(Company $company, bool $flush): void;

    public function save(Company $company, bool $flush): void;

    public function remove(Company $company, bool $flush): void;

    public function findOneByIdOrFail(string $id): Company;

    public function findOneByFantasyNameOrFail(string $fantasyName): Company;

    public function findByFantasyNameOrFail(string $fantasyName): array;

    public function findOneByTaxpayerOrFail(string $taxpayer): Company;

    public function existByTaxpayer(string $taxpayer): ?Company;

    public function existById(string $id): bool;

    public function search(CompanyFilter $filter): PaginatedResponse;
}
