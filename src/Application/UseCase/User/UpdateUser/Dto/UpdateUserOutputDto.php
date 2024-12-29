<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\UpdateUser\Dto;

use App\Domain\Model\User;

readonly class UpdateUserOutputDto
{
    private function __construct(public array $userData) {}

    public static function createFromModel(User $user): self
    {
        return new static([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'age' => $user->getAge(),
            'company' => [
                'id' => $user->getCompany()->getId(),
                'fantasyName' => $user->getCompany()->getFantasyName(),
                'taxpayer' => $user->getCompany()->getTaxpayer(),
            ],
            'isActive' => $user->isActive(),
        ]);
    }
}
