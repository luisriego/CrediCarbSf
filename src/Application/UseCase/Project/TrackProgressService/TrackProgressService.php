<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\TrackProgressService;

use App\Application\UseCase\Project\TrackProgressService\Dto\TrackProgressInputDto;
use App\Application\UseCase\Project\TrackProgressService\Dto\TrackProgressOutputDto;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Exception\Project\ProjectNotFoundException;
use App\Domain\Repository\ProjectRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TrackProgressService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    public function handle(TrackProgressInputDto $inputDto): TrackProgressOutputDto
    {
//        if (!$this->authorizationChecker->isGranted('ROLE_OPERATOR')) {
//            throw AccessDeniedException::UnauthorizedUser();
//        }

        $project = $this->projectRepository->findOneByIdOrFail($inputDto->projectId);

        $progress = $project->trackProgress();

        return new TrackProgressOutputDto(
            $progress['currentStatus'],
            $progress['milestones'],
            $progress['startDate'],
            $progress['endDate'],
            $progress['completionPercentage']
        );
    }
}