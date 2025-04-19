<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\AddUserToCompanyService;

use App\Application\UseCase\Company\AddUserToCompanyService\Dto\AddUserToCompanyInputDto;
use App\Application\UseCase\Company\AddUserToCompanyService\Dto\AddUserToCompanyOutputDto;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

class AddUserToCompanyService
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(AddUserToCompanyInputDto $inputDto): AddUserToCompanyOutputDto
    {
        /** @var Company $company */
        $company = $this->companyRepository->findOneByIdOrFail($inputDto->companyId);
        $user = $this->userRepository->findOneByIdOrFail($inputDto->userId);

        $user->assignToCompany($company);

        $company->assignUserToCompany($user);

        $this->companyRepository->save($company, true);

        return AddUserToCompanyOutputDto::create($inputDto->userId);
    }
}
