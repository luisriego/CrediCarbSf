<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByNameService;

use App\Adapter\Framework\Security\Voter\CompanyVoter;
use App\Application\UseCase\Company\GetCompanyByNameService\Dto\GetCompanyByNameInputDto;
use App\Application\UseCase\Company\GetCompanyByNameService\Dto\GetCompanyByNameOutputDto;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Repository\CompanyRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class GetCompanyByNameService
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    public function handle(GetCompanyByNameInputDto $inputDto): GetCompanyByNameOutputDto
    {
        $companies = $this->companyRepository->findByFantasyNameOrFail($inputDto->fantasyName);

        if (!$this->authorizationChecker->isGranted(CompanyVoter::GET_COMPANIES, $companies)) {
            throw AccessDeniedException::VoterFail();
        }

        return GetCompanyByNameOutputDto::create($companies);
    }
}