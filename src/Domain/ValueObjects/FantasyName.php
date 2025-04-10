<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use App\Domain\Exception\InvalidArgumentException;

final class FantasyName
{
    private const MIN_LENGTH = 5;
    private const MAX_LENGTH = 100;

    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    private function validate(string $value): void
    {
        $length = mb_strlen($value);
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Fantasy name must be between %d and %d characters', self::MIN_LENGTH, self::MAX_LENGTH)
            );
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}