<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\UpdateCompanyService\Dto;

use App\Domain\Model\Company;
use App\Domain\Validation\Traits\AssertNotEmptyTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;

class UpdateCompanyInputDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $fantasyName,
        public readonly string $userId,
    ) {}

    public static function create(
        string $id,
        string $fantasyName,
        string $userId): self
    {
        return new static($id, $fantasyName, $userId);
    }
}
