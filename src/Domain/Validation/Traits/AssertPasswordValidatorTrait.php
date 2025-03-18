<?php

declare(strict_types=1);

namespace App\Domain\Validation\Traits;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\User;

use function mb_strlen;
use function preg_match;
use function sprintf;

trait AssertPasswordValidatorTrait
{
    public function assertPassword(string $password): string
    {
        if (mb_strlen($password) < User::MIN_PASSWORD_LENGTH) {
            throw new InvalidArgumentException(sprintf('Password must be at least %d characters long.', User::MIN_PASSWORD_LENGTH));
        }

        if (mb_strlen($password) > User::MAX_PASSWORD_LENGTH) {
            throw new InvalidArgumentException(sprintf('Password must be at most %d characters long.', User::MAX_PASSWORD_LENGTH));
        }

        if (!preg_match('/\d/', $password)) {
            throw new InvalidArgumentException('Password must contain at least one number.');
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new InvalidArgumentException('Password must contain at least one uppercase letter.');
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new InvalidArgumentException('Password must contain at least one lowercase letter.');
        }

        return $password;
    }

    public function assertHashedPassword(string $password): bool
    {
        return mb_strlen($password) === 60;
    }
}
