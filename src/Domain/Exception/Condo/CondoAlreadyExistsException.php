<?php

declare(strict_types=1);

namespace App\Domain\Exception\Condo;

final class CondoAlreadyExistsException extends \DomainException
{
    public static function createFromTaxpayer(string $taxpayer): self
    {
        return new CondoAlreadyExistsException(\sprintf('Condo with taxpayer <%s> already exists', $taxpayer));
    }
}
