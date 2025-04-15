<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\UpdateCompanyService;

use App\Application\UseCase\Company\UpdateCompanyService\Dto\UpdateCompanyInputDto;
use App\Application\UseCase\Company\UpdateCompanyService\Dto\UpdateCompanyOutputDto;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Repository\CompanyRepositoryInterface;

readonly class UpdateCompanyService
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
    ) {}

    public function handle(UpdateCompanyInputDto $inputDto): UpdateCompanyOutputDto
    {
        if ($inputDto->company->fantasyName() === $inputDto->fantasyName) {
            throw new InvalidArgumentException('Fantasy name is the same');
        }

        $inputDto->company->updateFantasyName($inputDto->fantasyName);
        $this->companyRepository->save($inputDto->company, true);

        return UpdateCompanyOutputDto::create($inputDto->company);
    }
}
