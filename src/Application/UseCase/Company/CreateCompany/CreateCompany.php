<?php

namespace App\Application\UseCase\Company\CreateCompany;

use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyOutputDto;
use App\Domain\Bus\Event\EventBus;
use App\Domain\Model\Company;
use App\Domain\Policy\CompanyPolicyInterface;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;

readonly class CreateCompany
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private CompanyPolicyInterface     $companyPolicy,
        private EventBus $bus,
    ) {}

    public function create(
        CompanyId $id,
        CompanyTaxpayer $taxpayer,
        CompanyName $fantasyName
    ): void
    {
        $this->companyPolicy->canCreateOrFail();

        $this->companyRepository->validateTaxpayerUniqueness($taxpayer);

        $company = Company::create($id, $taxpayer, $fantasyName);

        $this->companyRepository->add($company, true);

        $this->bus->publish(...$company->pullDomainEvents());
    }
}