<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\GetCondoById\Dto;

use App\Domain\Model\Condo;

class GetCondoByIdOutputDto
{
    private function __construct(
        public readonly string $id,
        public readonly string $taxpayer,
        public readonly string $fantasyName,
        public readonly array $users,
    ) {
    }

    public static function create(Condo $condo): self
    {
        return new self(
            $condo->getId(),
            $condo->getTaxpayer(),
            $condo->getFantasyName(),
            $condo->getUsers()->toArray()
        );
    }
}
