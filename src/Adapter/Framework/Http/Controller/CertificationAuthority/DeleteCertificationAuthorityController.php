<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\CertificationAuthority;

use App\Application\UseCase\CertificationAuthority\DeleteCertificationAuthority\DeleteCertificationAuthorityService;
use App\Application\UseCase\CertificationAuthority\GetCertificationAuthorityById\Dto\DeleteCertificationAuthorityByIdInputDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

readonly class DeleteCertificationAuthorityController
{
    public function __construct(private DeleteCertificationAuthorityService $useCase) {}

    #[Route(
        path: '/api/certification-authority/{id}',
        name: 'certification-authority-delete',
        requirements: ['id' => '\b[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}\b'],
        methods: ['DELETE'],
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id): Response
    {
        $this->useCase->handle(DeleteCertificationAuthorityByIdInputDto::create($id));

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
