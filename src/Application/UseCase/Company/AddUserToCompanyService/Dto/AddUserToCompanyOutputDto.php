<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\AddUserToCompanyService\Dto;

readonly class AddUserToCompanyOutputDto
{
    public function __construct(
        public string $userId,
    ) {}

    public static function create(string $userId): self
    {
        return new static($userId);
    }
}
