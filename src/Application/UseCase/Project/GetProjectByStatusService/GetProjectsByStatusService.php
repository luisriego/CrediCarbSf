<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjectByStatusService;

use App\Application\UseCase\Project\GetProjectByStatusService\Dto\GetProjectsByStatusInputDto;
use App\Application\UseCase\Project\GetProjectByStatusService\Dto\GetProjectsByStatusOutputDto;
use App\Domain\Repository\ProjectRepositoryInterface;

final readonly class GetProjectsByStatusService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
    ) {}

    public function handle(GetProjectsByStatusInputDto $inputDto): GetProjectsByStatusOutputDto
    {
        return GetProjectsByStatusOutputDto::create(
            $this->projectRepository->findByStatus($inputDto->status),
        );
    }
}
