<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Mother;

use App\Domain\Model\Company;
use App\Domain\ValueObjects\FantasyName;
use App\Domain\ValueObjects\Taxpayer;
use App\Domain\ValueObjects\Uuid;

final class CompanyMother
{
    private const DEFAULT_TAXPAYER = '33592510002521';
    private const DEFAULT_FANTASY_NAME = 'Test Company';
    
    public static function create(
        ?Uuid $id = null,
        ?Taxpayer $taxpayer = null,
        ?FantasyName $fantasyName = null
    ): Company {
        return Company::create(
            $id ? $id->value() : Uuid::random()->value(),
            $taxpayer ? $taxpayer->getValue() : self::DEFAULT_TAXPAYER,
            $fantasyName ? $fantasyName->value() : self::DEFAULT_FANTASY_NAME
        );
    }

    public static function withInvalidTaxpayer(string $invalidTaxpayer): Company
    {
        return self::create(
            taxpayer: Taxpayer::fromString($invalidTaxpayer)
        );
    }

    public static function withInvalidFantasyName(string $invalidFantasyName): Company
    {
        return self::create(
            fantasyName: FantasyName::fromString($invalidFantasyName)
        );
    }

    public static function inactive(): Company
    {
        $company = self::create();
        $company->deactivate();
        return $company;
    }
    
    public static function withCustomData(
        string $taxpayer,
        string $fantasyName
    ): Company {
        return self::create(
            taxpayer: Taxpayer::fromString($taxpayer),
            fantasyName: FantasyName::fromString($fantasyName)
        );
    }
}