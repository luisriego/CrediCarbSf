<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\CertificationAuthority;

use App\Adapter\Framework\Http\Dto\CertificationAuthority\CreateCertificationAuthorityRequestDto;
use App\Application\UseCase\CertificationAuthority\CreateCertificationAuthorityService;
use App\Application\UseCase\CertificationAuthority\Dto\CreateCertificationAuthorityInputDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

readonly class CreateCertificationAuthorityController
{
    public function __construct(
        private CreateCertificationAuthorityService $createCertificationAuthority,
    ) {}

    #[Route('/api/certification-authority/create', name: 'certification_authority_create', methods: ['POST'])]
    #[IsGranted('ROLE_OPERATOR')]
    public function __invoke(CreateCertificationAuthorityRequestDto $requestDto): Response
    {
        $responseDto = $this->createCertificationAuthority->handle(
            CreateCertificationAuthorityInputDto::create(
                $requestDto->taxpayer,
                $requestDto->name,
                $requestDto->website,
            ),
        );

        return new JsonResponse(['certificationAuthorityId' => $responseDto->certificationAuthorityId], Response::HTTP_CREATED);
    }
}
