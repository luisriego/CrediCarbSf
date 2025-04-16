<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\CreateCompany\Dto;

final readonly class CreateCompanyInputDto
{
    private function __construct(
        public string $id,
        public string $taxpayer,
        public string $fantasyName,
    ) {}

    public static function create(
        string $id,
        string $taxpayer,
        string $fantasyName,
    ): self {
        return new self(
            $id,
            $taxpayer,
            $fantasyName,
        );
    }
}
