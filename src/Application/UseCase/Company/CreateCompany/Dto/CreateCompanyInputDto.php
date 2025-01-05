<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\CreateCompany\Dto;

use App\Domain\Validation\Traits\AssertTaxpayerValidatorTrait;
use App\Domain\Validation\Traits\AssertLengthRangeTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;

class CreateCompanyInputDto
{
    use AssertNotNullTrait;
    use AssertTaxpayerValidatorTrait;

    private const ARGS = [
        'fantasyName',
        'taxpayer',
    ];

    public string $fantasyName;
    public string $taxpayer;

    public function __construct(string $fantasyName, string $taxpayer)
    {
        $this->fantasyName = $fantasyName;
        $this->taxpayer = $this->cleanTaxpayer($taxpayer);

        $this->assertNotNull(self::ARGS, [$this->fantasyName, $this->taxpayer]);
        $this->assertValidTaxpayer($this->taxpayer);
    }

    public static function create(?string $fantasyName, ?string $taxpayer): self
    {
        return new static($fantasyName, $taxpayer);
    }
}
