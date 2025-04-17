<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Security\Voter\CompanyVoter;
use App\Application\UseCase\Company\GetCompanyByNameService\Dto\GetCompanyByNameInputDto;
use App\Application\UseCase\Company\GetCompanyByNameService\GetCompanyByNameService;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function count;

class GetCompanyByNameController extends AbstractController
{
    public function __construct(
        private readonly GetCompanyByNameService $useCase, private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    #[Route(
        '/api/company/by-name',
        name: 'get_company_by_name',
        methods: ['GET'],
    )]
    #[IsGranted("ROLE_USER")]
    public function index(string $name): Response
    {
        $companyReturned = $this->useCase->handle($name);

        if (!$this->authorizationChecker->isGranted(CompanyVoter::GET_COMPANY, $companyReturned)) {
            throw AccessDeniedException::VoterFail();
        }

        $head = (count($companyReturned) === 1) ? 'company' : 'companies';

        return new JsonResponse([$head => $companyReturned, 'quantity' => count($companyReturned)], Response::HTTP_OK);
    }
}
