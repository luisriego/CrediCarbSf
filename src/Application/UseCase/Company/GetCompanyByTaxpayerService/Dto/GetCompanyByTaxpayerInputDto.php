<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByTaxpayerService\Dto;

use App\Domain\Model\Company;
use App\Domain\Validation\Traits\AssertTaxpayerValidatorTrait;

class GetCompanyByTaxpayerInputDto
{
    use AssertTaxpayerValidatorTrait;

    public function __construct(
        public readonly Company $company,
    ) {
        $this->assertValidTaxpayer($this->company->getTaxpayer());
    }

    public static function create(Company $company): self
    {
        return new static($company);
    }
}
