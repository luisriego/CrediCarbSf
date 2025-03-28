<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\UpdateUser\Dto;

use App\Domain\Model\User;

class UpdateUserOutputDto
{
    private function __construct(public array $userData) {}

    public static function createFromModel(User $user): self
    {
        $company = $user->getCompany();

        return new static([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'age' => $user->getAge(),
            'company' => $company ? [
                'id' => $company->getId(),
                'fantasyName' => $company->fantasyName(),
                'taxpayer' => $company->taxPayer(),
            ] : null,
            'isActive' => $user->isActive(),
        ]);
    }
}
