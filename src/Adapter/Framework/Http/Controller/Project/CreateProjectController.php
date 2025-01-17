<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Project;

use App\Adapter\Framework\Http\Dto\Project\CreateProjectRequestDto;
use App\Application\UseCase\Project\CreateProjectService\CreateProjectService;
use App\Application\UseCase\Project\CreateProjectService\Dto\CreateProjectInputDto;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class CreateProjectController
{
    public function __construct(
        private readonly CreateProjectService $createProject, 
    ) {}

    #[Route('/api/project/create', name: 'project_create', methods: ['POST'])]
    public function __invoke(CreateProjectRequestDto $requestDto): Response
    {
        $responseDto = $this->createProject->handle(
            CreateProjectInputDto::create(
                $requestDto->name,
                $requestDto->description,
                $requestDto->areaHa,
                $requestDto->quantity,
                $requestDto->price,
                $requestDto->projectType,
            ),
        );

        return new JsonResponse(
            ['projectId' => $responseDto->projectId], 
            Response::HTTP_CREATED);
    }
}