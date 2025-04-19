<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\UpdateCompanyService\Dto;

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
        string $userId,
    ): self {
        return new static($id, $fantasyName, $userId);
    }
}
