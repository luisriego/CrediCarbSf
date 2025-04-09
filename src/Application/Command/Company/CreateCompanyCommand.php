<?php

namespace App\Application\Command\Company;

final readonly class CreateCompanyCommand
{
    public function __construct(
        private string $fantasyName,
        private string $taxpayer,
    ) {
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