<?php

declare(strict_types=1);

namespace App\Domain\Validation\Traits;

use App\Domain\Exception\InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

trait AssertValidUidTrait
{
    public function assertValidUid(string $uid): void
    {
        if (!Uuid::isValid($uid)) {
            throw new InvalidArgumentException('Invalid UID format');
        }
    }
}
