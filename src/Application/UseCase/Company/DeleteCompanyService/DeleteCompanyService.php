<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\DeleteCompanyService;

use App\Application\UseCase\Company\DeleteCompanyService\Dto\DeleteCompanyInputDto;
use App\Domain\Policy\CompanyPolicyInterface;
use App\Domain\Repository\CompanyRepositoryInterface;

readonly class DeleteCompanyService
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepo,
        private CompanyPolicyInterface $companyPolicy,
    ) {}

    public function handle(DeleteCompanyInputDto $inputDto): void
    {
        $this->companyPolicy->canDeleteOrFail(companyId: $inputDto->id);

        $companyToDelete = $this->companyRepo->findOneByIdOrFail($inputDto->id);

        $this->companyRepo->remove($companyToDelete, true);
    }
}
