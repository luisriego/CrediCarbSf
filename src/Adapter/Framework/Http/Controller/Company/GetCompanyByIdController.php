<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Security\Voter\CompanyVoter;
use App\Application\UseCase\Company\GetCompanyByIdService\Dto\GetCompanyByIdInputDto;
use App\Application\UseCase\Company\GetCompanyByIdService\GetCompanyByIdService;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Repository\CompanyRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class GetCompanyByIdController
{
    public function __construct(
        private readonly GetCompanyByIdService $useCase,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    #[Route(
        '/api/company/{id}',
        name: 'get_company_by_id',
        requirements: ['id' => '^.{36}$'],
        methods: ['GET'],
    )]
    public function index(string $id): Response
    {
        $company = $this->companyRepository->findOneByIdOrFail($id);

        $inputDto = GetCompanyByIdInputDto::create($company);

        if (!$this->authorizationChecker->isGranted(CompanyVoter::DELETE_COMPANY, $company)) {
            throw AccessDeniedException::VoterFail();
        }

        $companyReturned = $this->useCase->handle($inputDto);

        return new JsonResponse(['company' => $companyReturned->data], Response::HTTP_OK);
    }
}
