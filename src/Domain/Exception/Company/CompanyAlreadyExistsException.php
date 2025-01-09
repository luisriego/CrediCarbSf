<?php

declare(strict_types=1);

namespace App\Domain\Exception\Company;

use Symfony\Component\HttpKernel\Exception\HttpException;

use function sprintf;

final class CompanyAlreadyExistsException extends HttpException
{
    public static function createFromTaxPayer(string $taxPayer): self
    {
        return new self(400, sprintf('Company with tax payer <%s> already exists', $taxPayer));
    }

    public static function createFromFantasyName(string $email): self
    {
        return new self(400, sprintf('Company with Name <%s> already exists', $email));
    }
}
