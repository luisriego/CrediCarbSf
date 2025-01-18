<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\CreateProjectService;

use App\Application\UseCase\Project\CreateProjectService\Dto\CreateProjectInputDto;
use App\Application\UseCase\Project\CreateProjectService\Dto\CreateProjectOutputDto;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Exception\Project\ProjectAlreadyExistsException;
use App\Domain\Model\Project;
use App\Domain\Repository\ProjectRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CreateProjectService
{
    public function __construct(
        private ProjectRepositoryInterface $projectReporitory,
        private AuthorizationCheckerInterface $authorizationChecker,
        private readonly ProjectRepositoryInterface $projectRepository,
    ) {}

    public function handle(CreateProjectInputDto $inputDto): CreateProjectOutputDto
    {
        if (!$this->authorizationChecker->isGranted('ROLE_OPERATOR')) {
            throw AccessDeniedException::UnauthorizedUser();
        }

        $project = Project::create(
            $inputDto->name,
            $inputDto->description,
            $inputDto->areaHa,
            $inputDto->quantity,
            $inputDto->price,
            $inputDto->projectType,
        );

        if ($this->projectRepository->exists($project)) {
            throw ProjectAlreadyExistsException::repeatedProject();
        }

        $this->projectReporitory->add($project, true);

        return new CreateProjectOutputDto($project->getId());
    }
}
