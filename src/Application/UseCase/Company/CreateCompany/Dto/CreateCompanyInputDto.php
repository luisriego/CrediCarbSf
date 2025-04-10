<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\CreateCompany\Dto;

use App\Domain\ValueObjects\Uuid;

final class CreateCompanyInputDto
{
    private function __construct(
        public readonly string $id,
        public readonly string $taxpayer,
        public readonly string $fantasyName,
    ) {}

    public static function create(
        ?string $id = null,
        string $taxpayer,
        string $fantasyName,
    ): self {
        return new self(
            $id ?? Uuid::random()->value(),
            $taxpayer,
            $fantasyName,
        );
    }
}