<?php

declare(strict_types=1);

namespace App\Domain\Exception\User;

use DomainException;

class AccessDeniedException extends DomainException
{
    public static function VoterFail(): AccessDeniedException
    {
        return new AccessDeniedException('Access denied from Voter', 403);
    }

    public static function UserNotLogged(): AccessDeniedException
    {
        return new AccessDeniedException('User not logged, please LoginService and try again', 401);
    }
}
