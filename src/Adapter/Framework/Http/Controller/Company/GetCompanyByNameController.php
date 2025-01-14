<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Security\Voter\CompanyVoter;
use App\Application\UseCase\Company\GetCompanyByNameService\Dto\GetCompanyByNameInputDto;
use App\Application\UseCase\Company\GetCompanyByNameService\GetCompanyByNameService;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Repository\CompanyRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class GetCompanyByNameController extends AbstractController
{
    public function __construct(
        private readonly GetCompanyByNameService $useCase,
    ) {}
    
    #[Route(
        '/api/company/by-name/{name}',
        name: 'get_company_by_name',
        methods: ['GET'],
    )]
    public function index(string $name): Response
    {
        $inputDto = GetCompanyByNameInputDto::create($name);

        $companyReturned = $this->useCase->handle($inputDto);

        $head = (count($companyReturned->data) === 1) ? "company" : "companies";

        return new JsonResponse([$head => $companyReturned->data, 'quantity' => count($companyReturned->data)], Response::HTTP_OK);
    }
}
