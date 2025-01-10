<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\DeleteCompanyService;

use App\Application\UseCase\Company\DeleteCompanyService\Dto\DeleteCompanyInputDto;
use App\Domain\Repository\CompanyRepositoryInterface;

class DeleteCompanyService
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepo,
    ) {}

    public function handle(DeleteCompanyInputDto $inputDto): void
    {
        $companyToDelete = $this->companyRepo->findOneByIdOrFail($inputDto->id);

        $this->companyRepo->remove($companyToDelete, true);
    }
}
