<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Validation\Traits\AssertTaxpayerValidatorTrait;

class Taxpayer
{
    use AssertTaxpayerValidatorTrait;
    private string $value;

    protected function __construct(
        private readonly string $taxpayer,
    ) {
        if (!$taxpayer) {
            throw InvalidArgumentException::createFromMessage('The Taxpayer identifier cannot be empty.');
        }

        $this->value = $this->validTaxpayer($taxpayer);
    }

    public static function fromString(string $taxpayer): self
    {
        return new static($taxpayer);
    }

    public function value(): string
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