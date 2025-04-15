<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\UseCase\Company\Mother;

use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyInputDto;
use App\Domain\Model\Company;
use App\Domain\ValueObject\Uuid;


final class CreateCompanyInputDtoMother
{
    public static function create(
        ?string $id = null,
        ?string $taxpayer = null,
        ?string $fantasyName = null
    ): CreateCompanyInputDto {
        return CreateCompanyInputDto::create(
            $id ?? Uuid::random()->value(),
            $taxpayer ?? '33592510002521',
            $fantasyName ?? 'Test Company'
        );
    }

    public static function fromCompany(Company $company): CreateCompanyInputDto
    {
        return CreateCompanyInputDto::create(
            $company->id(),
            $company->taxpayer(),
            $company->fantasyName()
        );
    }

    public static function withEmptyTaxpayer(): CreateCompanyInputDto
    {
        return self::create(
            taxpayer: ''
        );
    }

    public static function withEmptyFantasyName(): CreateCompanyInputDto
    {
        return self::create(
            fantasyName: ''
        );
    }

    public static function withRandomData(): CreateCompanyInputDto
    {
        return self::create(
            Uuid::random()->value(),
            self::generateValidCnpj(),
            'Test Company ' . uniqid()
        );
    }

    /**
     * Generates a valid Brazilian CNPJ
     */
    private static function generateValidCnpj(): string
    {
        // Base numbers (8 digits)
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);

        // Fixed branch number
        $n9 = 0;
        $n10 = 0;
        $n11 = 0;
        $n12 = 1;

        // First verification digit
        $d1 = $n12 * 2 + $n11 * 3 + $n10 * 4 + $n9 * 5 + $n8 * 6 + $n7 * 7 + $n6 * 8 + $n5 * 9 + $n4 * 2 + $n3 * 3 + $n2 * 4 + $n1 * 5;
        $d1 = 11 - ($d1 % 11);
        if ($d1 >= 10) {
            $d1 = 0;
        }

        // Second verification digit
        $d2 = $d1 * 2 + $n12 * 3 + $n11 * 4 + $n10 * 5 + $n9 * 6 + $n8 * 7 + $n7 * 8 + $n6 * 9 + $n5 * 2 + $n4 * 3 + $n3 * 4 + $n2 * 5 + $n1 * 6;
        $d2 = 11 - ($d2 % 11);
        if ($d2 >= 10) {
            $d2 = 0;
        }

        // Format CNPJ
        return sprintf(
            '%d%d%d%d%d%d%d%d%d%d%d%d%d%d',
            $n1, $n2, $n3, $n4, $n5, $n6, $n7, $n8, $n9, $n10, $n11, $n12, $d1, $d2
        );
    }

}
