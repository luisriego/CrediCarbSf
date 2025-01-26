<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\Dto;

use App\Domain\Validation\Traits\AssertNotEmptyTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;

class CreateCertificationAuthorityInputDto
{
    use AssertNotNullTrait;
    use AssertNotEmptyTrait;

    private const ARGS = [
        'name',
        'website',
    ];

    public function __construct(
        public ?string $name,
        public ?string $website,
    ) {
        $this->assertNotNull(self::ARGS, [
            $this->name,
            $this->website,
        ]);

        $this->assertNotEmpty(self::ARGS, [
            $this->name,
            $this->website,
        ]);
    }

    public static function create(
        ?string $name,
        ?string $website,
    ): self {
        return new static($name, $website);
    }
}
