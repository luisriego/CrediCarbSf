<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\CreateCertificationAuthority\Dto;

use App\Domain\Validation\Traits\AssertNotEmptyTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;
use App\Domain\Validation\Traits\AssertTaxpayerValidatorTrait;

class CreateCertificationAuthorityInputDto
{
    use AssertNotNullTrait;
    use AssertNotEmptyTrait;
    use AssertTaxpayerValidatorTrait;

    private const ARGS = [
        'taxpayer',
        'name',
        'website',
    ];

    public function __construct(
        public ?string $taxpayer,
        public ?string $name,
        public ?string $website,
    ) {
        $this->assertNotNull(self::ARGS, [
            $this->taxpayer,
            $this->name,
            $this->website,
        ]);

        $this->assertNotEmpty(self::ARGS, [
            $this->taxpayer,
            $this->name,
            $this->website,
        ]);

        $this->assertValidTaxpayer($this->taxpayer);
        $this->taxpayer = $this->cleanTaxpayer($this->taxpayer);
    }

    public static function create(
        ?string $taxpayer,
        ?string $name,
        ?string $website,
    ): self {
        return new static($taxpayer, $name, $website);
    }
}
