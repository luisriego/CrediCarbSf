<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjectsByTypeService;

use App\Application\UseCase\Project\GetProjectsByTypeService\Dto\GetProjectsByTypeInputDto;
use App\Application\UseCase\Project\GetProjectsByTypeService\Dto\GetProjectsByTypeIOutputDto;
use App\Domain\Repository\ProjectRepositoryInterface;

final readonly class GetProjectsByTypeService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
    ) {}

    public function handle(GetProjectsByTypeInputDto $inputDto): GetProjectsByTypeIOutputDto
    {
        return GetProjectsByTypeIOutputDto::create(
            $this->projectRepository->findByType($inputDto->type),
        );
    }
}
