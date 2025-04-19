<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\CertificationAuthority;

use App\Adapter\Framework\Http\Dto\CertificationAuthority\UpdateCertificationAuthorityRequestDto;
use App\Application\UseCase\CertificationAuthority\UpdateCertificationAuthority\Dto\UpdateCertificationAuthorityInputDto;
use App\Application\UseCase\CertificationAuthority\UpdateCertificationAuthority\UpdateCertificationAuthorityService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

readonly class UpdateCertificationAuthorityController
{
    public function __construct(private UpdateCertificationAuthorityService $updateCertificationAuthority) {}

    #[Route(
        path: '/api/v1/certification-authorities/{id}',
        name: 'certification_authority_update',
        requirements: ['id' => '\b[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}\b'],
        methods: ['PATCH'],
    )]
    #[IsGranted('ROLE_OPERATOR')]
    public function __invoke(UpdateCertificationAuthorityRequestDto $requestDto, string $id): Response
    {
        $responseDto = $this->updateCertificationAuthority->handle(
            UpdateCertificationAuthorityInputDto::create(
                $id,
                $requestDto->name,
                $requestDto->website,
            ),
        );

        return new JsonResponse(['certificationAuthority' => $responseDto->certificationAuthority->toArray()], Response::HTTP_OK);
    }
}
