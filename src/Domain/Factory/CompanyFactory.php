<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Model\Company;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;

class CompanyFactory
{
    public static function create(string $id, string $taxpayer, string $fantasyName): Company
    {
        return Company::create(
            CompanyId::fromString($id),
            CompanyTaxpayer::fromString($taxpayer),
            CompanyName::fromString($fantasyName),
        );
    }
}
