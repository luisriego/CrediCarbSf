<?php

namespace App\Application\Command\Company;

use App\Domain\ValueObjects\Uuid;
use App\Domain\ValueObjects\FantasyName;
use App\Domain\ValueObjects\Taxpayer;


final readonly class CreateCompanyCommand
{
    public function __construct(
        private Uuid $id,
        private Taxpayer $taxpayer,
        private FantasyName $fantasyName,
    ) {
    }

    public static function create(
        ?string $id = null,
        string $taxpayer,
        string $fantasyName
    ): self {
        return new self(
            $id ? Uuid::fromString($id) : Uuid::random(),
            Taxpayer::fromString($taxpayer),
            FantasyName::fromString($fantasyName)
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function fantasyName(): string
    {
        return $this->fantasyName;
    }

    public function taxpayer(): string
    {
        return $this->taxpayer;
    }
}