<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetAllProjectsService;

use App\Domain\Exception\AccessDeniedException;
use App\Domain\Repository\ProjectRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use function array_map;

final readonly class GetAllProjectsService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    public function handle(): array
    {
        if (!$this->authorizationChecker->isGranted('ROLE_OPERATOR')) {
            throw AccessDeniedException::UnauthorizedUser();
        }

        $projects = $this->projectRepository->findAll();

        return array_map(function ($project) {
            return [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'description' => $project->getDescription(),
                'startDate' => $project->getStartDate(),
                'endDate' => $project->getEndDate(),
            ];
        }, $projects);
    }
}
