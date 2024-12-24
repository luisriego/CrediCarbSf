<?php

declare(strict_types=1);

namespace App\Domain\Exception\User;

final class UserTokenInvalidException extends \DomainException
{
    public static function FromToken(string $token): self
    {
        return new UserTokenInvalidException(\sprintf('Activation Token <%s> is invalid, user not activated', $token));
    }
}
