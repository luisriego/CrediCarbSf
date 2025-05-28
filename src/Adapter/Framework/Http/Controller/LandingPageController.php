<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class LandingPageController extends AbstractController
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    #[Route('/', name: 'landing_page', methods: ['GET'])]
    public function __invoke(): Response
    {
        $filePath = $this->projectDir . '/public/landing/index.html'; // Updated path

        if (!file_exists($filePath)) {
            // Consider a more specific error or log if this happens post-deployment
            throw new NotFoundHttpException('React landing page index.html not found.');
        }

        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new \RuntimeException('Could not read React landing page index.html.');
        }

        return new Response(
            $content,
            Response::HTTP_OK,
            ['Content-Type' => 'text/html']
        );
    }
}
