<?php

declare(strict_types=1);

namespace App\Domain\Exception\User;

final class UserAlreadyExistsException extends \DomainException
{
    public static function createFromEmail(string $email): self
    {
        return new UserAlreadyExistsException(\sprintf('User with email <%s> already exists', $email));
    }
}
