<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\User;

use function mb_strlen;
use function preg_match;
use function sprintf;

class UserName extends Name
{
    public static function fromString(string $name): self
    {
        return new self($name);
    }

    protected function validate(string $value): void
    {
        $length = mb_strlen($value);

        if ($length < User::NAME_MIN_LENGTH || $length > User::NAME_MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'Company name must be between %d and %d characters',
                    User::NAME_MIN_LENGTH, User::NAME_MAX_LENGTH
                ),
            );
        }

        if (preg_match('/[<>{}[\]()\/\\\^*!?+~`|=]/', $value)) {
            throw new InvalidArgumentException(
                'User name contains disallowed special characters',
            );
        }

        if (preg_match('/[<>{}[\]()\/\\\\^*!?+~`|=]/', $value)) {
            throw new InvalidArgumentException(
                'User name contains disallowed special characters');
        }
    }
}
