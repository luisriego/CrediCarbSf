<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Http\Dto\Company\UpdateCompanyRequestDto;
use App\Adapter\Framework\Security\Voter\CompanyVoter;
use App\Application\UseCase\Company\UpdateCompanyService\Dto\UpdateCompanyInputDto;
use App\Application\UseCase\Company\UpdateCompanyService\UpdateCompanyService;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UpdateCompanyController
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly UpdateCompanyService $useCase,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    #[Route('/api/company/{id}', name: 'update_company', methods: ['PATCH'])]
    public function invoke(UpdateCompanyRequestDto $requestDto, string $id): Response
    {
        /** @var Company $companyToUpdate */
        $companyToUpdate = $this->companyRepository->findOneByIdOrFail($id);

        $inputDto = UpdateCompanyInputDto::create(
            $requestDto->fantasyName,
            $companyToUpdate,
        );

        if (!$this->authorizationChecker->isGranted(CompanyVoter::UPDATE_COMPANY, $companyToUpdate)) {
            throw AccessDeniedException::UnauthorizedUser();
        }

        $responseDto = $this->useCase->handle($inputDto);

        return new JsonResponse($responseDto->company->toArray(), Response::HTTP_OK);
    }
}
