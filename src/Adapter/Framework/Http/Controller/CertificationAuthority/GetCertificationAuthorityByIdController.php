<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\CertificationAuthority;

use App\Application\UseCase\CertificationAuthority\GetCertificationAuthorityById\Dto\GetCertificationAuthorityByIdInputDto;
use App\Application\UseCase\CertificationAuthority\GetCertificationAuthorityById\GetCertificationAuthorityByIdService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class GetCertificationAuthorityByIdController
{
    public function __construct(
        private GetCertificationAuthorityByIdService $useCase,
    ) {}

    #[Route(
        path: '/api/v1/certification-authorities/{id}',
        name: 'certification-authority-get',
        requirements: ['id' => '\b[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}\b'],
        methods: ['GET'],
    )]
    public function __invoke(string $id): Response
    {
        $inputDto = GetCertificationAuthorityByIdInputDto::create($id);

        $response = $this->useCase->handle($inputDto);

        return new JsonResponse(['CertificationAuthority' => $response->data], Response::HTTP_OK);
    }
}
