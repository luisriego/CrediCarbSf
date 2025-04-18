<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Security\Voter\CompanyVoter;
use App\Application\UseCase\Company\GetCompanyByTaxpayerService\Dto\GetCompanyByTaxpayerInputDto;
use App\Application\UseCase\Company\GetCompanyByTaxpayerService\GetCompanyByTaxpayerService;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Repository\CompanyRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

readonly class GetCompanyByTaxpayerController
{
    public function __construct(
        private GetCompanyByTaxpayerService $useCase
    ) {}

    #[Route(
        '/api/company/{taxpayer}',
        name: 'get_company_by_taxpayer',
        requirements: ['taxpayer' => '^(?!health-check$)(\d{11}|\d{14})$'],
        methods: ['GET'],
    )]
    #[IsGranted('ROLE_USER')]
    public function index(string $taxpayer): Response
    {
        $companyReturned = $this->useCase->handle($taxpayer);

        return new JsonResponse(['company' => $companyReturned->data], Response::HTTP_OK);
    }
}
