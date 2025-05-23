<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use InvalidArgumentException as NativeInvalidArgumentException;

use function implode;
use function sprintf;

class InvalidArgumentException extends NativeInvalidArgumentException
{
    public static function createFromMessage(string $message): self
    {
        return new static($message);
    }

    public static function createFromArgument(string $argument): self
    {
        return new static(sprintf('Invalid argument [%s]', $argument));
    }

    public static function createFromArray(array $arguments): self
    {
        return new static(sprintf('Invalid arguments [%s]', implode(', ', $arguments)));
    }

    public static function createFromMinAndMaxLength(int $min, int $max): self
    {
        return new static(sprintf('Value must be min [%d] and max [%d] characters', $min, $max));
    }

    public static function createFromEmailValidation(string $email): self
    {
        return new static(sprintf('The email "%s" is not valid.', $email));
    }

    public static function createFromMin(int $min): self
    {
        return new static(sprintf('Value must be min [%d]', $min));
    }
}
