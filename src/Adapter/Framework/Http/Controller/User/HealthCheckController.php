<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\User;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController
{
    #[Route('/api/v1/users/health-check', name: 'user_health_check', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['message' => 'Module User up and running!']);
    }
}
