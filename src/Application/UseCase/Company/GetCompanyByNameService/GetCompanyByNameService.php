<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByNameService;

use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\CompanyName;

readonly class GetCompanyByNameService
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
    ) {}

    /**
     * @return Company[] Array of Company objects
     */
    public function handle(string $name): array
    {
        $companyName = CompanyName::fromString($name);

        return $this->companyRepository->findByFantasyNameOrFail($companyName->value());
    }
}
