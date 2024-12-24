<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\GetUserById\Dto;

use App\Domain\Model\User;

class GetUserByIdOutputDto
{
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly int $age,
    ) {
    }

    public static function create(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getAge(),
        );
    }
}
