<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\DeleteCompanyService\Dto;

readonly class DeleteCompanyInputDto
{
    private function __construct(
        public string $id,
        public string $userId,
    ) {}

    public static function create(?string $id, string $userId): self
    {
        return new static($id, $userId);
    }
}
