<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\AddUserToCompanyService\Dto;

class AddUserToCompanyOutputDto
{
    public string $userId;

    private function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public static function create(string $userId): self
    {
        return new static($userId);
    }
}