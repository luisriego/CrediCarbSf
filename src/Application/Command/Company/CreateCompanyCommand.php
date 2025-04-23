<?php

declare(strict_types=1);

namespace App\Application\Command\Company;

use App\Domain\Bus\Command\Command;

final readonly class CreateCompanyCommand implements Command
{
    public function __construct(
        private string $id,
        private string $taxpayer,
        private string $fantasyName,
    ) {}

    public static function create(
        string $id,
        string $taxpayer,
        string $fantasyName,
    ): self {
        return new self($id, $taxpayer, $fantasyName);
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
