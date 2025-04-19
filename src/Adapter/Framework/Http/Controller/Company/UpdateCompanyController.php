<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Http\Dto\Company\UpdateCompanyRequestDto;
use App\Application\UseCase\Company\UpdateCompanyService\Dto\UpdateCompanyInputDto;
use App\Application\UseCase\Company\UpdateCompanyService\UpdateCompanyService;
use App\Domain\Model\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class UpdateCompanyController
{
    public function __construct(
        private UpdateCompanyService $useCase,
        private Security $security,
    ) {}

    #[Route('/api/v1/companies/{id}', name: 'update_company', methods: ['PATCH'])]
    public function invoke(UpdateCompanyRequestDto $requestDto, string $id): Response
    {
        /** @var User $user */
        $user = $this->security->getToken()->getUser();

        $inputDto = UpdateCompanyInputDto::create(
            $id,
            $requestDto->fantasyName,
            $user->getId(),
        );

        $responseDto = $this->useCase->handle($inputDto);

        return new JsonResponse($responseDto->company, Response::HTTP_OK);
    }
}
