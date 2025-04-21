<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Http\Dto\Company\CreateCompanyRequestDto;
use App\Application\UseCase\Company\CreateCompany\CreateCompanyService;
use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyInputDto;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class CreateCompanyController
{
    public function __construct(
        private CreateCompanyService          $createCompany,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    #[Route('/api/v1/companies/create', name: 'company_create', methods: ['POST'])]
    public function __invoke(CreateCompanyRequestDto $requestDto): JsonResponse
    {
        if (!$this->authorizationChecker->isGranted('ROLE_OPERATOR')) {
            throw AccessDeniedException::UnauthorizedUser();
        }

        $responseDto = $this->createCompany->handle(
            CreateCompanyInputDto::create(
                $requestDto->id,
                $requestDto->taxpayer,
                $requestDto->fantasyName,
            ),
        );

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
