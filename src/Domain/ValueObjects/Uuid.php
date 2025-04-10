<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;
use Stringable;

use function sprintf;

class Uuid implements Stringable
{
    public function __construct(protected readonly string $value)
    {
        $this->ensureIsValidUuid($value);
    }

    public static function fromString(string $id): self
    {
        return new static($id);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function random(): self
    {
        return new static(\Symfony\Component\Uid\Uuid::v4()->toRfc4122());
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Uuid $other): bool
    {
        return $this->value() === $other->value();
    }

    private function ensureIsValidUuid(string $id): void
    {
        if (!\Symfony\Component\Uid\Uuid::isValid($id)) {
            throw new InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $id));
        }
    }
}
