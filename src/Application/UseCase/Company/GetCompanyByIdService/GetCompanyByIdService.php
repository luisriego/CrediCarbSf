<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByIdService;

use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\CompanyId;

readonly class GetCompanyByIdService
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
    ) {}

    public function handle(string $id): Company
    {
        $companyId = CompanyId::fromString($id);

        return $this->companyRepository->findOneByIdOrFail($companyId->value());
    }
}
