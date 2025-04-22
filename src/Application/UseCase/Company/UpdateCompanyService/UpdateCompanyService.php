<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\UpdateCompanyService;

use App\Application\UseCase\Company\UpdateCompanyService\Dto\UpdateCompanyInputDto;
use App\Application\UseCase\Company\UpdateCompanyService\Dto\UpdateCompanyOutputDto;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Policy\CompanyPolicyInterface;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

readonly class UpdateCompanyService
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private UserRepositoryInterface $userRepository,
        private CompanyPolicyInterface $companyPolicy,
    ) {}

    public function handle(UpdateCompanyInputDto $inputDto): UpdateCompanyOutputDto
    {
        $user = $this->userRepository->findOneByIdOrFail($inputDto->userId);

        $this->companyPolicy->canUpdateOrFail($inputDto->id);

        $company = $this->companyRepository->findOneByIdOrFail($inputDto->id);

        if ($company->fantasyName() === $inputDto->fantasyName) {
            throw new InvalidArgumentException('Fantasy name is the same');
        }

        $company->updateFantasyName($inputDto->fantasyName);
        $this->companyRepository->save($company, true);

        return UpdateCompanyOutputDto::create($company->id());
    }
}
