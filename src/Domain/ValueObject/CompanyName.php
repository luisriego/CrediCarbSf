<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;

use function mb_strlen;
use function preg_match;
use function sprintf;

class CompanyName extends FantasyName
{
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    protected function validate(string $value): void
    {
        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'Company name must be between %d and %d characters',
                    self::MIN_LENGTH, self::MAX_LENGTH),
            );
        }

        if (preg_match('/[<>{}[\]()\/\\\\^*!?+~`|=]/', $value)) {
            throw new InvalidArgumentException(
                'Company name contains disallowed special characters');
        }
    }
}
