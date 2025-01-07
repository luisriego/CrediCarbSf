<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\GetUserByIdService\Dto;

use App\Domain\Model\User;

class GetUserByIdOutputDto
{
    private function __construct(public array $data) {}

    public static function create(User $user): self
    {
        $company = $user->getCompany();

        return new self(
            [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'age' => $user->getAge(),
                'company' => $company ? [
                    'id' => $company->getId(),
                    'fantasyName' => $company->getFantasyName(),
                    'taxpayer' => $company->getTaxpayer(),
                ] : null,
                'isActive' => $user->isActive(),
            ],
        );
    }
}
