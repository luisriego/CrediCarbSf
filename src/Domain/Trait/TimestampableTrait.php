<?php

declare(strict_types=1);

namespace App\Domain\Trait;

trait TimestampableTrait
{
    use CreatedOnTrait;
    use UpdatedOnTrait;
}
