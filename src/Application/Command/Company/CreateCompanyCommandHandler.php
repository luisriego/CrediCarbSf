<?php

namespace App\Application\Command\Company;

use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObjects\Taxpayer;

final readonly class CreateCompanyCommandHandler
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository
    ) {
    }

    public function __invoke(CreateCompanyCommand $command): void
    {
        $taxpayer = Taxpayer::fromString($command->taxpayer());

        // this verification may be on Domain layer, I need to study where and how;
        // by now i'll do that in repo with existByTaxpayerOrFail
        $this->companyRepository->validateTaxpayerUniqueness($taxpayer);

        $company = Company::create(
            $command->id(),
            $taxpayer,
            $command->fantasyName(),
        );

        $this->companyRepository->save($company, true);
    }
}
