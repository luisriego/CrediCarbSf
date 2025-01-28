<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\UpdateCertificationAuthority\Dto;

use App\Domain\Validation\Traits\AssertNotEmptyTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;
use App\Domain\Validation\Traits\AssertValidUidTrait;

class UpdateCertificationAuthorityInputDto
{
    use AssertNotNullTrait;
    use AssertNotEmptyTrait;
    use AssertValidUidTrait;

    private const ARGS = [
        'name',
        'website',
    ];

    public function __construct(
        public ?string $id,
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
        ?string $id,
        ?string $name,
        ?string $website,
    ): self {
        return new static($id, $name, $website);
    }
}
