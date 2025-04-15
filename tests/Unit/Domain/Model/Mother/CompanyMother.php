<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Mother;

use App\Domain\Model\Company;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;
use App\Domain\ValueObject\FantasyName;
use App\Domain\ValueObject\Taxpayer;
use App\Domain\ValueObject\Uuid;

final class CompanyMother
{
    private const DEFAULT_ID = 'cbf070cc-80d2-4bfe-9fa6-a96b97ffb0da';
    private const DEFAULT_TAXPAYER = '33592510002521';
    private const DEFAULT_FANTASY_NAME = 'Test Company';
    
    public static function create(
        ?string $id = null,
        ?string $taxpayer = null,
        ?string $fantasyName = null
    ): Company {
        return Company::create(
            CompanyId::fromString($id ?? self::DEFAULT_ID),
            CompanyTaxpayer::fromString($taxpayer ?? self::DEFAULT_TAXPAYER),
            CompanyName::fromString($fantasyName ?? self::DEFAULT_FANTASY_NAME)
        );
    }

    public static function withInvalidTaxpayer(string $invalidTaxpayer): Company
    {
        return self::create(
            taxpayer: $invalidTaxpayer
        );
    }

    public static function withInvalidFantasyName(string $invalidFantasyName): Company
    {
        return self::create(
            fantasyName: $invalidFantasyName
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
            taxpayer: $taxpayer,
            fantasyName: $fantasyName
        );
    }
}