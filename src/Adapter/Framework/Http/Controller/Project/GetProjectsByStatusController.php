<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Project;

use App\Adapter\Framework\Http\Dto\Project\GetProjectsByStatusRequestDto;
use App\Application\UseCase\Project\GetProjectByStatusService\Dto\GetProjectsByStatusInputDto;
use App\Application\UseCase\Project\GetProjectByStatusService\GetProjectsByStatusService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function count;

readonly class GetProjectsByStatusController
{
    public function __construct(private GetProjectsByStatusService $useCase) {}

    #[Route(
        '/api/v1/projectss/{status}',
        name: 'get_projects_by_status',
        requirements: ['status' => '(?i)PLANNED|IN_DEVELOPMENT|APPROVED|IN_EXECUTION|COMPLETED|CANCELED|SUSPENDED'],
        methods: ['GET'],
    )]
    #[IsGranted('ROLE_USER', statusCode: 403)]
    public function __invoke(GetProjectsByStatusRequestDto $requestDto, string $status): Response
    {
        try {
            $inputDto = GetProjectsByStatusInputDto::create($status);
            $response = $this->useCase->handle($inputDto);

            return new JsonResponse(['projects' => count($response->data), 'data' => $response->data], Response::HTTP_OK);
        } catch (Exception) {
            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
