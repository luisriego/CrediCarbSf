<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Http\Dto\Company\CreateCompanyRequestDto;
use App\Application\UseCase\Company\CreateCompany\CreateCompany;
use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyInputDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateCompanyController
{
    public function __construct(private readonly CreateCompany $createCompany) {}

    #[Route('/company/create', name: 'company_create', methods: ['POST'])]
    public function __invoke(CreateCompanyRequestDto $requestDto): JsonResponse
    {
        //        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $responseDto = $this->createCompany->handle(
            CreateCompanyInputDto::create(
                $requestDto->fantasyName,
                $requestDto->taxpayer,
            ),
        );

        return new JsonResponse(['CompanyId' => $responseDto->companyId], Response::HTTP_CREATED);
    }
}
