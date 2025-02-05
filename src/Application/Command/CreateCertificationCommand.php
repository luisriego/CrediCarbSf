<?php

declare(strict_types=1);

namespace App\Application\Command;

class CreateCertificationCommand
{
    public function __construct(
        private readonly string  $name,
        private readonly ?string $description,
        private readonly string $type
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }
}