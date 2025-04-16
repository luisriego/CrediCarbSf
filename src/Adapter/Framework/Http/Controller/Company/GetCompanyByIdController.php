<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Security\Voter\CompanyVoter;
use App\Application\UseCase\Company\GetCompanyByIdService\GetCompanyByIdService;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class GetCompanyByIdController
{
    public function __construct(
        private GetCompanyByIdService $useCase,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    #[Route(
        '/api/company/{id}',
        name: 'get_company_by_id',
        requirements: ['id' => '^.{36}$'],
        methods: ['GET'],
    )]
    public function __invoke(string $id): Response
    {
        $companyReturned = $this->useCase->handle($id);

        if (!$this->authorizationChecker->isGranted(CompanyVoter::DELETE_COMPANY, $companyReturned)) {
            throw AccessDeniedException::VoterFail();
        }

        return new JsonResponse(['company' => $companyReturned->toArray()], Response::HTTP_OK);
    }
}
