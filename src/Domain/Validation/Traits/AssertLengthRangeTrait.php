<?php

declare(strict_types=1);

namespace App\Domain\Validation\Traits;

use App\Domain\Exception\InvalidArgumentException;

use function mb_strlen;

trait AssertLengthRangeTrait
{
    public function assertValueRangeLength(string $value, int $min, int $max): void
    {
        if (mb_strlen($value) < $min || mb_strlen($value) > $max) {
            throw InvalidArgumentException::createFromMinAndMaxLength($min, $max);
        }
    }

    public function isValueRangeLengthValid(string $value, int $min, int $max): bool
    {
        if (mb_strlen($value) < $min || mb_strlen($value) > $max) {
            return false;
        }

        return true;
    }
}
