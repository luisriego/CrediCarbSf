<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Http\Dto\Company\AddUserToCompanyRequestDto;
use App\Application\UseCase\Company\AddUserToCompanyService\AddUserToCompanyService;
use App\Application\UseCase\Company\AddUserToCompanyService\Dto\AddUserToCompanyInputDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class AddUserToCompanyController
{
    public function __construct(
        private AddUserToCompanyService $addUserToCompany,
    ) {}

    #[Route('/api/v1/companies/adduser/{id}', name: 'company_add_user', methods: ['POST'])]
    public function __invoke(AddUserToCompanyRequestDto $request, string $id): Response
    {
        $responseDto = $this->addUserToCompany->handle(
            AddUserToCompanyInputDto::create(
                $id,
                $request->userId,
            ),
        );

        return new JsonResponse(['userId' => $responseDto->userId], Response::HTTP_CREATED);
    }
}
