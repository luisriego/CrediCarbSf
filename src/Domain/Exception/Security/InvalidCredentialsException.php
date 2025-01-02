<?php

declare(strict_types=1);

namespace App\Domain\Exception\Security;

use DomainException;

class InvalidCredentialsException extends DomainException
{
    public static function FromLogin(): self
    {
        return new InvalidCredentialsException(sprintf('Invalid email or password'));
    }
}