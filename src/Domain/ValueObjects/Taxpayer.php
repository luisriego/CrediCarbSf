<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use App\Domain\Validation\Traits\AssertTaxpayerValidatorTrait;

class Taxpayer
{
    use AssertTaxpayerValidatorTrait;
    private string $value;

    private function __construct(
        private readonly string $taxpayer,
    ) {
        $this->assertValidTaxpayer($taxpayer);
        $this->value = $this->cleanTaxpayer($taxpayer);
    }

    public static function fromString(string $taxpayer): self
    {
        return new self($taxpayer);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}