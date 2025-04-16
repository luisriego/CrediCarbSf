<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CompanyId extends Uuid
{
    public static function fromString(string $id): self
    {
        return new static($id);
    }
}
