<?php

declare(strict_types=1);

namespace App\Domain\Exception\User;

final class UserAlreadyIsMemberException extends \DomainException
{
    public static function fromId(string $id): self
    {
        return new UserAlreadyIsMemberException(\sprintf('The User with Id <%s> is already a member of this Condominium', $id));
    }
}
