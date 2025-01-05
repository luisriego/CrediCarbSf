<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\CreateCompany;

use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyInputDto;
use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyOutputDto;
use App\Domain\Exception\Company\CompanyAlreadyExistsException;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;

class CreateCompany
{
    public function __construct(private CompanyRepositoryInterface $companyRepository) {}

    public function handle(CreateCompanyInputDto $inputDto): CreateCompanyOutputDto
    {
        if (empty($inputDto->taxpayer)) {
            throw new InvalidArgumentException('The following fields cannot be empty: taxpayer');
        }

        if (empty($inputDto->fantasyName)) {
            throw new InvalidArgumentException('The following fields cannot be empty: fantasyName');
        }

        if (null !== $this->companyRepository->existByTaxpayer($inputDto->taxpayer)) {
            throw CompanyAlreadyExistsException::createFromTaxPayer($inputDto->taxpayer);
        }

        $company = Company::create(
            $inputDto->taxpayer,
            $inputDto->fantasyName,
        );

        $this->companyRepository->add($company, true);

        return new CreateCompanyOutputDto($company->getId());
    }
}
