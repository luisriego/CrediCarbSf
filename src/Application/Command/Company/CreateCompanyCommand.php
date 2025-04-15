<?php

namespace App\Application\Command\Company;

use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;
use App\Domain\ValueObject\Uuid;
use App\Domain\ValueObject\FantasyName;
use App\Domain\ValueObject\Taxpayer;


final readonly class CreateCompanyCommand
{
    public function __construct(
        private CompanyId $id,
        private CompanyTaxpayer $taxpayer,
        private CompanyName $fantasyName,
    ) {
    }

    public static function create(
        string $id,
        string $taxpayer,
        string $fantasyName
    ): self {
        return new self(
            CompanyId::fromString($id),
            CompanyTaxpayer::fromString($taxpayer),
            CompanyName::fromString($fantasyName)
        );
    }

    public function id(): CompanyId
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