<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Project;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController
{
    #[Route('/project/health-check', name: 'project_health_check', methods: ['GET'])]
    public function healthCheck(): JsonResponse
    {
        return new JsonResponse(['message' => 'Module Project up and running!']);
    }
}
