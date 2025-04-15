<?php

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;

class CompanyTaxpayer extends Taxpayer
{
    public static function fromString(string $taxpayer): self
    {
        return new static($taxpayer);
    }
}