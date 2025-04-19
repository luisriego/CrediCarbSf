<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Project;

use App\Application\UseCase\Project\GetProjectByIdService\Dto\GetProjectByIdInputDto;
use App\Application\UseCase\Project\GetProjectByIdService\GetProjectByIdService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class GetProjectByIdController
{
    public function __construct(
        private GetProjectByIdService $useCase,
    ) {}

    #[Route('/api/v1/projects/{id}', name: 'get_project_by_id', requirements: ['id' => '\b[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}\b'], methods: ['GET'])]
    public function __invoke(string $id): Response
    {
        $inputDto = GetProjectByIdInputDto::create($id);

        $response = $this->useCase->handle($inputDto);

        return new JsonResponse(['project' => $response], Response::HTTP_OK);
    }
}
