<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Project;

use App\Adapter\Framework\Http\Dto\Project\GetProjectsByTypeRequestDto;
use App\Application\UseCase\Project\GetProjectsByTypeService\Dto\GetProjectsByTypeInputDto;
use App\Application\UseCase\Project\GetProjectsByTypeService\GetProjectsByTypeService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function count;

readonly class GetProjectsByTypeController
{
    public function __construct(private GetProjectsByTypeService $useCase) {}

    #[Route(
        '/api/v1/projectss/{type}',
        'get-projects-by-type',
        requirements: ['type' => '(?i)Reforestation|Afforestation|Agroforestry|ForestConservation|RenewableEnergy|EnergyEfficiency|MethaneCapture|SoilCarbonSequestration|ImprovedCookstoves|FuelSwitching|Bioenergy|BlueCarbon|CarbonCaptureAndStorage|SustainableAgriculture|UrbanGreening'],
        methods: ['GET'],
    )]
    #[IsGranted('ROLE_USER', statusCode: 403)]
    public function __invoke(GetProjectsByTypeRequestDto $requestDto, string $type): Response
    {
        try {
            $inputDto = GetProjectsByTypeInputDto::create($type);
            $response = $this->useCase->handle($inputDto);

            return new JsonResponse(['projects' => count($response->data), 'data' => $response->data], Response::HTTP_OK);
        } catch (Exception) {
            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
