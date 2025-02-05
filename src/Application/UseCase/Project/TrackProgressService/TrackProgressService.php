<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\TrackProgressService;

use App\Application\UseCase\Project\TrackProgressService\Dto\TrackProgressInputDto;
use App\Application\UseCase\Project\TrackProgressService\Dto\TrackProgressOutputDto;
use App\Domain\Repository\ProjectRepositoryInterface;

readonly class TrackProgressService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository
    ) {}

    public function handle(TrackProgressInputDto $inputDto): TrackProgressOutputDto
    {
        $project = $this->projectRepository->findOneByIdOrFail($inputDto->projectId);

        $progress = $project->trackProgress();

        return new TrackProgressOutputDto(
            $progress['currentStatus'],
            $progress['milestones'],
            $progress['startDate'],
            $progress['endDate'],
            $progress['completionPercentage'],
        );
    }
}
