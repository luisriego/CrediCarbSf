<?php

namespace App\Application\Command\Company;

use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;

final readonly class CreateCompanyCommandHandler
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository
    ) {
    }

    public function __invoke(CreateCompanyCommand $command): void
    {
        $this->companyRepository->validateTaxpayerUniqueness($command->taxpayer());

        $company = Company::create(
            $command->id(),
            $command->taxpayer(),
            $command->fantasyName(),
        );

        $this->companyRepository->save($company, true);
    }
}
