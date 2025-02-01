<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjectsByCompanyService\Dto;

class GetProjectsByCompanyInputDto
{
    private const ARGS = ['companyId'];

    public function __construct(
        public readonly ?string $companyId,
    ) {}

    public static function create(?string $companyId): self
    {
        return new static($companyId);
    }
}
