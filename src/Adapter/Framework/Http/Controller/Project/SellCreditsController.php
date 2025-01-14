<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Project;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SellCreditsController
{
    #[Route('/api/project/sell-credits', name: 'project_sell_credits', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new JsonResponse(['message' => 'Module Project up and running!']);
    }
}