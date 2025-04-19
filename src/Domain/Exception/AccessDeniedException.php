<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use DomainException;

class AccessDeniedException extends DomainException
{
    public static function VoterFail(): self
    {
        return new AccessDeniedException('Access denied from Voter', 403);
    }

    public static function UserNotLogged(): self
    {
        return new AccessDeniedException('User not logged, please LoginService and try again', 401);
    }

    public static function UnauthorizedUser(): self
    {
        return new AccessDeniedException('The User has not the necessary permissions', 403);
    }
}
