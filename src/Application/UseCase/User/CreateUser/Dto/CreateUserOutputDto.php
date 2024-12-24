<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\CreateUser\Dto;

class CreateUserOutputDto
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
