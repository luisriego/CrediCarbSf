<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\CreateCompany;

use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyInputDto;
use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyOutputDto;
use App\Domain\Exception\Company\CompanyAlreadyExistsException;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;

readonly class CreateCompany
{
    public function __construct(private CompanyRepositoryInterface $companyRepository) {}

    public function handle(CreateCompanyInputDto $inputDto): CreateCompanyOutputDto
    {
        $this->companyRepository->validateTaxpayerUniqueness($inputDto->taxpayer);

        $company = Company::create(
            CompanyId::fromString($inputDto->id),
            CompanyTaxpayer::fromString($inputDto->taxpayer),
            CompanyName::fromString($inputDto->fantasyName),
        );

        $this->companyRepository->add($company, true);

        return new CreateCompanyOutputDto($company->getId());
    }
}
