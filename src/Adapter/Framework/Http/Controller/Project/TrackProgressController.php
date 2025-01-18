<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Project;

use App\Adapter\Framework\Http\Dto\Project\TrackProgressRequestDto;
use App\Application\UseCase\Project\TrackProgressService\Dto\TrackProgressInputDto;
use App\Application\UseCase\Project\TrackProgressService\TrackProgressService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TrackProgressController
{
    public function __construct(
        private readonly TrackProgressService $trackProgressService,
    ) {}

    #[Route('/api/project/{projectId}/progress', name: 'project_track_progress', methods: ['GET'])]
    public function __invoke(TrackProgressRequestDto $requestDto): Response
    {
        $responseDto = $this->trackProgressService->handle(
            TrackProgressInputDto::create($requestDto->projectId),
        );

        return new JsonResponse([
            'currentStatus' => $responseDto->currentStatus,
            'milestones' => $responseDto->milestones,
            'startDate' => $responseDto->startDate,
            'endDate' => $responseDto->endDate,
            'completionPercentage' => $responseDto->completionPercentage,
        ], Response::HTTP_OK);
    }
}
