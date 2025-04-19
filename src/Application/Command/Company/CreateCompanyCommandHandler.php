<?php

declare(strict_types=1);

namespace App\Application\Command\Company;

use App\Domain\Bus\Command\CommandHandler;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;

final readonly class CreateCompanyCommandHandler implements CommandHandler
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
    ) {}

    public function __invoke(CreateCompanyCommand $command): void
    {
        $this->companyRepository->validateTaxpayerUniqueness($command->taxpayer());

        $company = Company::create(
            CompanyId::fromString($command->id()),
            CompanyTaxpayer::fromString($command->taxpayer()),
            CompanyName::fromString($command->fantasyName()),
        );

        $this->companyRepository->save($company, true);
    }
}
