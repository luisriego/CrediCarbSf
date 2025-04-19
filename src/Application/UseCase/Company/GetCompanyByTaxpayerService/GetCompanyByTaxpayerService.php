<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByTaxpayerService;

use App\Application\UseCase\Company\GetCompanyByTaxpayerService\Dto\GetCompanyByTaxpayerOutputDto;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\CompanyTaxpayer;

readonly class GetCompanyByTaxpayerService
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepo,
    ) {
    }

    public function handle(string $taxpayer): GetCompanyByTaxpayerOutputDto
    {
        $company = $this->companyRepo->findOneByTaxpayerOrFail(
            CompanyTaxpayer::fromString($taxpayer)->value());

        return GetCompanyByTaxpayerOutputDto::create($company);
    }
}
