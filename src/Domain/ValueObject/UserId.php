<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\Uuid;

class UserId extends Uuid
{
    public static function fromString(string $id): self
    {
        return new static($id);
    }
}
