<?php

namespace App\Domain\Factory;

use App\Domain\Model\Company;
use App\Domain\ValueObjects\FantasyName;
use App\Domain\ValueObjects\Taxpayer;
use App\Domain\ValueObjects\Uuid;

class CompanyFactory
{
    public static function create(string $id, string $taxpayer, string $fantasyName): Company
    {
        return Company::create(
            $id ?? Uuid::random()->value(),
            $taxpayer,
            $fantasyName,
        );
    }
}