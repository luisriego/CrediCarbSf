<?php

declare(strict_types=1);

namespace App\Application\Command\Company;

use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;

final readonly class CreateCompanyCommand
{
    public function __construct(
        private CompanyId $id,
        private CompanyTaxpayer $taxpayer,
        private CompanyName $fantasyName,
    ) {}

    public static function create(
        string $id,
        string $taxpayer,
        string $fantasyName,
    ): self {
        return new self(
            CompanyId::fromString($id),
            CompanyTaxpayer::fromString($taxpayer),
            CompanyName::fromString($fantasyName),
        );
    }

    public function id(): string
    {
        return $this->id->value();
    }

    public function fantasyName(): string
    {
        return $this->fantasyName->value();
    }

    public function taxpayer(): string
    {
        return $this->taxpayer->value();
    }
}
