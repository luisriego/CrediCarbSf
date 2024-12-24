<?php

declare(strict_types=1);

namespace App\Domain\Exception\User;

class AccessDeniedException extends \DomainException
{
    public static function VoterFail()
    {
        return new AccessDeniedException('Access denied from Voter', 403);
    }
}
