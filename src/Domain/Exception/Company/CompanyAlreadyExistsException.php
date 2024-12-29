<?php

declare(strict_types=1);

namespace App\Domain\Exception\Company;

use DomainException;

use function sprintf;

final class CompanyAlreadyExistsException extends DomainException
{
    public static function createFromTaxPayer(string $taxPayer): self
    {
        return new CompanyAlreadyExistsException(sprintf('Company with tax payer <%s> already exists', $taxPayer));
    }

    public static function createFromEmail(string $email): self
    {
        return new CompanyAlreadyExistsException(sprintf('Company with email <%s> already exists', $email));
    }
}
