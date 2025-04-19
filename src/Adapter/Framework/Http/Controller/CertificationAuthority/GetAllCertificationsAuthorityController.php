<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\CertificationAuthority;

use App\Application\UseCase\CertificationAuthority\GetAllCertificationsAuthority\GetAllCertificationsAuthorityService;
use App\Domain\Model\CertificationAuthority;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

readonly class GetAllCertificationsAuthorityController
{
    public function __construct(
        private GetAllCertificationsAuthorityService $getAllCertificationsAuthorityService,
    ) {}

    #[Route('/api/v1/certification-authorities/all', name: 'certification-authority-get-all', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(): Response
    {
        /** @var CertificationAuthority[] $authorities */
        $authorities = $this->getAllCertificationsAuthorityService->handle();

        return new JsonResponse(['certificationAuthorities' => $authorities], status: Response::HTTP_OK);
    }
}
