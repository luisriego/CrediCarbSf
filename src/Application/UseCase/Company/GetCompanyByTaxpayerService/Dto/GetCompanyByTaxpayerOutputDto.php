<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByTaxpayerService\Dto;

use App\Domain\Model\Company;

class GetCompanyByTaxpayerOutputDto
{
    private function __construct(public array $data) {}

    public static function create(Company $company): self
    {
        return new self(
            [
                'id' => $company->getId(),
                'fantasyName' => $company->fantasyName(),
                'taxpayer' => $company->taxPayer(),
            ],
        );
    }
}
