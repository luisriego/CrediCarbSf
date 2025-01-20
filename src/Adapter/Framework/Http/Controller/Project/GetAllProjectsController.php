<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Project;

use App\Application\UseCase\Project\GetAllProjectsService\GetAllProjectsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class GetAllProjectsController
{
    public function __construct(
        private GetAllProjectsService $useCase,
    ) {}

    #[Route('/api/project/all', name: 'get_projects', methods: ['GET'])]
    public function __invoke(): Response
    {
        $responseDto = $this->useCase->handle();

        return new JsonResponse(['projects' => $responseDto], Response::HTTP_OK);
    }
}
