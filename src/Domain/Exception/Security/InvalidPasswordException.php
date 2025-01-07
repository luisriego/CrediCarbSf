<?php

declare(strict_types=1);

namespace App\Domain\Exception\Security;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidPasswordException extends BadRequestHttpException
{
    public static function invalidLength(): never
    {
        throw new self('Password must be at least 6 characters');
    }

    public static function oldPasswordDoesNotMatch(): never
    {
        throw new self('Old password does not match');
    }
}
