<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\CreateCompany\Dto;

use App\Domain\Validation\Traits\AssertNotNullTrait;
use App\Domain\Validation\Traits\AssertTaxpayerValidatorTrait;

class CreateCompanyInputDto
{
    use AssertNotNullTrait;
    use AssertTaxpayerValidatorTrait;

    private const ARGS = [
        'fantasyName',
        'taxpayer',
    ];

    public string $id;
    public string $fantasyName;
    public string $taxpayer;

    public function __construct(string $id, string $fantasyName, string $taxpayer)
    {
        $this->id = $id;
        $this->fantasyName = $fantasyName;
        $this->taxpayer = $this->cleanTaxpayer($taxpayer);

        $this->assertNotNull(self::ARGS, [$this->fantasyName, $this->taxpayer]);
        $this->assertValidTaxpayer($this->taxpayer);
    }

    public static function create(string $id, ?string $fantasyName, ?string $taxpayer): self
    {
        return new static($id, $fantasyName, $taxpayer);
    }
}
