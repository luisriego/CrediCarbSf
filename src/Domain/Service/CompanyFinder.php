<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;

final readonly class CompanyFinder
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository
    ) {
    }

    /**
     * Find companies by partial name match
     *
     * @param string $name The name or partial name to search for
     * @return array<Company> List of matching companies
     */
    public function findByName(string $name): array
    {
        return $this->companyRepository->findByFantasyNameOrFail($name);
    }

    /**
     * Find a single company by its exact taxpayer ID
     *
     * @param string $taxpayerId The taxpayer ID to search for
     * @return Company|null The matching company or null if not found
     */
    public function findByTaxpayer(string $taxpayerId): ?Company
    {
        return $this->companyRepository->findOneByTaxpayerOrFail($taxpayerId);
    }

    /**
     * Find active companies by partial name match
     *
     * @param string $name The name or partial name to search for
     * @return array<Company> List of matching active companies
     */
    public function findActiveByName(string $name): array
    {
        $companies = $this->findByName($name);

        return array_filter($companies, function (Company $company): bool {
            return $company->isActive();
        });
    }
}