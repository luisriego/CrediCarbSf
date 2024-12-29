<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\ActivateUser\Dto;

use App\Domain\Model\User;

class ActivateUserOutputDto
{
    private function __construct(public readonly array $userData) {}

    public static function createFromModel(User $user): self
    {
        return new static([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'age' => $user->getAge(),
            'isActive' => $user->isActive(),
            'token' => $user->getToken(),
        ]);
    }
}
