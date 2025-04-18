<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Http\Dto\Company\GetCompanyByNameRequestDto;
use App\Adapter\Framework\Security\Voter\CompanyVoter;
use App\Application\UseCase\Company\GetCompanyByNameService\Dto\GetCompanyByNameInputDto;
use App\Application\UseCase\Company\GetCompanyByNameService\GetCompanyByNameService;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Model\Company;
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
        private readonly GetCompanyByNameService $useCase,
    ) {}

    #[Route(
        '/api/company/by-name',
        name: 'get_company_by_name',
        methods: ['GET'],
    )]
    #[IsGranted("ROLE_USER")]
    public function index(GetCompanyByNameRequestDto $requestDto): Response
    {
        $companiesReturned = $this->useCase->handle($requestDto->name);

        $companies = array_map(
            static fn (Company $company): array => [
                'id' => $company->id(),
                'taxpayer' => $company->taxpayer(),
                'fantasyName' => $company->fantasyName(),
                'isActive' => $company->isActive(),
                'createdOn' => $company->getCreatedOn()->format('Y-m-d H:i:s'),
            ],
            $companiesReturned
        );
    
        $head = (count($companies) === 1) ? 'company' : 'companies';
    
        return new JsonResponse(
            [
                $head => $companies,
                'quantity' => count($companies)
            ],
            Response::HTTP_OK
        );
    }
}
