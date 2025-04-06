<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use App\Domain\Exception\InvalidArgumentException;

use function preg_match;

class Password
{
    public function __construct(
        private string $value,
    ) {
        $this->assertValidPassword($this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Password $other): bool
    {
        return $this->value() === $other->value();
    }

    private function assertValidPassword(string $value): void
    {
        $length = mb_strlen($value);

        if ($length < 6 || $length > 100) {
            throw InvalidArgumentException::createFromMessage('Password length must be between 6 and 100 characters.');
        }

        if (!preg_match('/[a-z]/', $value)) {
            throw InvalidArgumentException::createFromMessage('Password must include at least one lowercase letter.');
        }

        if (!preg_match('/[A-Z]/', $value)) {
            throw InvalidArgumentException::createFromMessage('Password must include at least one uppercase letter.');
        }

        if (!preg_match('/[0-9]/', $value)) {
            throw InvalidArgumentException::createFromMessage('Password must include at least one number.');
        }

        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'\"\\\|,.<>\/?]/', $value)) {
            throw InvalidArgumentException::createFromMessage('Password must include at least one special character.');
        }
    }
}
