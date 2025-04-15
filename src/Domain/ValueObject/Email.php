<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;

use function filter_var;

use const FILTER_VALIDATE_EMAIL;

class Email
{
    public function __construct(
        private string $value,
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw InvalidArgumentException::createFromMessage('Invalid email address');
        }
        $this->value = mb_strtolower($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value() === $other->value();
    }
}
