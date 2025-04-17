<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByNameService;

use App\Adapter\Framework\Security\Voter\CompanyVoter;
use App\Application\UseCase\Company\GetCompanyByNameService\Dto\GetCompanyByNameInputDto;
use App\Application\UseCase\Company\GetCompanyByNameService\Dto\GetCompanyByNameOutputDto;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\CompanyName;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class GetCompanyByNameService
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
    ) {}

    /**
     * @return Company[] Array of Company objects
     */
    public function handle(string $name): array
    {
        $companyName = CompanyName::fromString($name);

        return $this->companyRepository->findByFantasyNameOrFail($companyName->value());
    }
}
