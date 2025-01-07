<?php

declare(strict_types=1);

namespace App\Domain\Validation\Traits;

use App\Domain\Exception\InvalidArgumentException;

use function filter_var;

use const FILTER_VALIDATE_EMAIL;

trait AssertValidEmailTrait
{
    public function assertValidEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw InvalidArgumentException::createFromEmailValidation($email);
        }
    }
}
