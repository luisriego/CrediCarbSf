<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjectsByCompanyService;

use App\Application\UseCase\Project\GetProjectsByCompanyService\Dto\GetProjectsByCompanyInputDto;
use App\Application\UseCase\Project\GetProjectsByCompanyService\Dto\GetProjectsByCompanyOutputDto;
use App\Domain\Repository\ProjectRepositoryInterface;

final readonly class GetProjectsByCompanyService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
    ) {}

    public function handle(GetProjectsByCompanyInputDto $inputDto): GetProjectsByCompanyOutputDto
    {
        return GetProjectsByCompanyOutputDto::create(
            $this->projectRepository->findByCompany($inputDto->companyId),
        );
    }
}
