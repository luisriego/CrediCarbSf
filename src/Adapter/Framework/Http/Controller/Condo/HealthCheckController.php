<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Condo;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController extends AbstractController
{
    #[Route('/condo/health-check', name: 'condo_health_check', priority: 2, methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->json(['message' => 'Module Condo up and running!']);
    }
}
