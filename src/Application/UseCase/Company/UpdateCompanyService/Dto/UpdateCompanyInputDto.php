<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\UpdateCompanyService\Dto;

use App\Domain\Model\Company;
use App\Domain\Validation\Traits\AssertNotEmptyTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;

class UpdateCompanyInputDto
{
    use AssertNotNullTrait;
    use AssertNotEmptyTrait;

    private const ARGS = [
        'fantasyName',
    ];

    public function __construct(
        public readonly string $fantasyName,
        public readonly Company $company,
    ) {
        $this->assertNotNull(self::ARGS, [$this->fantasyName]);
        $this->assertNotEmpty(self::ARGS, [$this->fantasyName]);
    }

    public static function create(?string $fantasyName, Company $company): self
    {
        return new static($fantasyName, $company);
    }
}
