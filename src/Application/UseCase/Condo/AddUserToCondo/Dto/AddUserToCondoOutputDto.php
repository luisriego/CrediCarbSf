<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\AddUserToCondo\Dto;

use App\Domain\Model\Condo;

class AddUserToCondoOutputDto
{
    private function __construct(public readonly array $condoData)
    {
    }

    public static function createFromModel(Condo $condo): self
    {
        return new static([
            'id' => $condo->getId(),
            'taxpayer' => $condo->getTaxpayer(),
            'fantasyName' => $condo->getFantasyName(),
            'isActive' => $condo->isActive(),
            'users' => $condo->getUsers()->getValues(),
        ]);
    }
}
