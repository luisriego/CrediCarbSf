<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;

use function array_filter;

final readonly class CompanyFinder
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
    ) {}

    public function findByName(string $name): array
    {
        return $this->companyRepository->findByFantasyNameOrFail($name);
    }

    public function findByTaxpayer(string $taxpayerId): ?Company
    {
        return $this->companyRepository->findOneByTaxpayerOrFail($taxpayerId);
    }

    public function findActiveByName(string $name): array
    {
        $companies = $this->findByName($name);

        return array_filter($companies, function (Company $company): bool {
            return $company->isActive();
        });
    }
}
