<?php

declare(strict_types=1);

namespace App\Domain\Validation\Traits;

use App\Domain\Exception\InvalidArgumentException;
use DateTimeImmutable;

trait AssertValidDateTimeTrait
{
    public function assertValidDateTime(string $dateTime, string $format = 'Y-m-d H:i:s'): void
    {
        $date = DateTimeImmutable::createFromFormat($format, $dateTime);

        if ($date === false || $date->format($format) !== $dateTime) {
            throw new InvalidArgumentException('Invalid date time format');
        }
    }
}
