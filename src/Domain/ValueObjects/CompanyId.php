<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final class CompanyId extends Uuid
{
    public static function random(): self
    {
        return new self(Uuid::random()->value());
    }
}