<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\DeleteCompanyService;

use App\Application\UseCase\Company\DeleteCompanyService\Dto\DeleteCompanyInputDto;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Policy\CompanyPolicyInterface;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

readonly class DeleteCompanyService
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepo,
        private UserRepositoryInterface $userRepository,
        private CompanyPolicyInterface $companyPolicy,
    ) {}

    public function handle(DeleteCompanyInputDto $inputDto): void
    {
        $user = $this->userRepository->findOneByIdOrFail($inputDto->userId);

        if (!$this->companyPolicy->canDelete($user, $inputDto->id)) {
            throw AccessDeniedException::UnauthorizedUser();
        }

        if (!$this->companyRepo->existById($inputDto->id)) {
            throw new ResourceNotFoundException("Company not found with id: {$inputDto->id}");
        }

        $companyToDelete = $this->companyRepo->findOneByIdOrFail($inputDto->id);

        $this->companyRepo->remove($companyToDelete, true);
    }
}
