<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByNameService\Dto;

use App\Domain\Model\Company;
use App\Domain\Validation\Traits\AssertLengthRangeTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;
use App\Domain\Validation\Traits\AssertValidUidTrait;

class GetCompanyByNameInputDto
{
    use AssertNotNullTrait;
    use AssertLengthRangeTrait;

    private const ARGS = [
        'fantasyName',
    ];

    public function __construct(
        public readonly string $fantasyName,
    ) {
        $this->assertNotNull(self::ARGS, [$this->fantasyName]);
    }

    public static function create(?string $fantasyName): self
    {
        return new static($fantasyName);
    }
}