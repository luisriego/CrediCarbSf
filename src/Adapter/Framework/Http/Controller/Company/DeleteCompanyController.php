<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Security\Voter\CompanyVoter;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Repository\CompanyRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DeleteCompanyController
{
    public function __construct(
        public readonly CompanyRepositoryInterface $companyRepo,
        public readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    #[Route('/api/company/{id}', name: 'delete_company', methods: ['DELETE'])]
    public function invoke(string $id): Response
    {
        $companyToDelete = $this->companyRepo->findOneByIdOrFail($id);

        if (!$this->authorizationChecker->isGranted(CompanyVoter::DELETE_COMPANY, $companyToDelete)) {
            throw AccessDeniedException::UnauthorizedUser();
        }

        $this->companyRepo->remove($companyToDelete, true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
