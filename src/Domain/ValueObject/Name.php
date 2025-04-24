<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;

use function mb_strlen;
use function sprintf;

class Name
{
    protected const MIN_LENGTH = 5;
    protected const MAX_LENGTH = 100;

    protected function __construct(
        private readonly string $value,
    ) {
        $this->validate($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    private function validate(string $value): void
    {
        if (empty($value)) {
            throw InvalidArgumentException::createFromMessage('The Company Name cannot be empty.');
        }

        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'Fantasy name must be between %d and %d characters',
                    self::MIN_LENGTH,
                    self::MAX_LENGTH,
                ),
            );
        }
    }
}
