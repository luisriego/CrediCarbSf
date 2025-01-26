<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Project;

use App\Adapter\Framework\Http\Dto\Project\GetProjectsByCompanyRequestDto;
use App\Application\UseCase\Project\GetProjectsByCompanyService\Dto\GetProjectsByCompanyInputDto;
use App\Application\UseCase\Project\GetProjectsByCompanyService\GetProjectsByCompanyService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function count;

class GetProjectsByCompanyController extends AbstractController
{
    public function __construct(private readonly GetProjectsByCompanyService $useCase) {}

    #[Route(
        path: '/api/projects/{company}',
        name: 'get-projects-by-company',
        requirements: ['company' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}'],
        methods: ['GET'],
    )]
    #[IsGranted('ROLE_USER', statusCode: Response::HTTP_UNAUTHORIZED)]
    public function __invoke(GetProjectsByCompanyRequestDto $requestDto, string $company): Response
    {
        try {
            $inputDto = GetProjectsByCompanyInputDto::create($company);
            $response = $this->useCase->handle($inputDto);

            return new JsonResponse(
                ['projects' => count($response->data), 'data' => $response->data],
                Response::HTTP_OK,
            );
        } catch (Exception) {
            return new JsonResponse(
                ['error' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
