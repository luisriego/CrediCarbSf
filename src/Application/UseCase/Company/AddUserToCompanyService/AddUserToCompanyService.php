<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\AddUserToCompanyService;

use App\Application\UseCase\Company\AddUserToCompanyService\Dto\AddUserToCompanyInputDto;
use App\Application\UseCase\Company\AddUserToCompanyService\Dto\AddUserToCompanyOutputDto;
use App\Domain\Model\Company;
use App\Domain\Policy\CompanyPolicyInterface;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

readonly class AddUserToCompanyService
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private UserRepositoryInterface $userRepository,
        private CompanyPolicyInterface $companyPolicy,
    ) {}

    public function handle(AddUserToCompanyInputDto $inputDto): AddUserToCompanyOutputDto
    {
        /** @var Company $company */
        $company = $this->companyRepository->findOneByIdOrFail($inputDto->companyId);
        $user = $this->userRepository->findOneByIdOrFail($inputDto->userId);

        $this->companyPolicy->canAddUserOrFail($company->id());

        $user->assignToCompany($company);

        $company->assignUserToCompany($user);

        $this->companyRepository->save($company, true);

        return AddUserToCompanyOutputDto::create($inputDto->userId);
    }
}
