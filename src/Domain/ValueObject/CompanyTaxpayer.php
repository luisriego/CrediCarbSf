<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class CompanyTaxpayer extends Taxpayer
{
    public static function fromString(string $taxpayer): self
    {
        return new static($taxpayer);
    }
}
