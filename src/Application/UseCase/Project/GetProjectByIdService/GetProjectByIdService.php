<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjectByIdService;

use App\Application\UseCase\Project\GetProjectByIdService\Dto\GetProjectByIdInputDto;
use App\Application\UseCase\Project\GetProjectByIdService\Dto\GetProjectByIdOutputDto;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Repository\ProjectRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class GetProjectByIdService
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    public function handle(GetProjectByIdInputDto $inputDto): GetProjectByIdOutputDto
    {
        if (!$this->authorizationChecker->isGranted('ROLE_OPERATOR')) {
            throw AccessDeniedException::UnauthorizedUser();
        }

        return GetProjectByIdOutputDto::create(
            $this->projectRepository->findOneByIdOrFail($inputDto->id),
        );
    }
}
