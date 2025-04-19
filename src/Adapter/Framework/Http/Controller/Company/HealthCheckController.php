<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController
{
    #[Route('/api/v1/companies/health-check', name: 'company_health_check', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['message' => 'Module Company up and running!']);
    }
}
