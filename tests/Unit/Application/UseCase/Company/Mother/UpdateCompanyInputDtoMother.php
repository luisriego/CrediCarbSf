<?php

namespace App\Tests\Unit\Application\UseCase\Company\Mother;

use App\Application\UseCase\Company\UpdateCompanyService\Dto\UpdateCompanyInputDto;

final class UpdateCompanyInputDtoMother
{
    private const VALID_COMPANY_ID = '3f46050a-bd15-419a-a37d-0867ec8c504b';
    private const USER_ID = '3f46050a-bd15-419a-a37d-0867ec8c504b';
    private const NON_EXISTING_ID = 'e7b6c15d-fa92-47bc-8289-9db382ae0378';
    private const INVALID_ID = '3f46050a-bd15-419a-a37d-0867ec8c5@#$';
    private const DEFAULT_FANTASY_NAME = 'Updated Company Name';

    public static function withValidData(
        string $companyId = self::VALID_COMPANY_ID,
        string $fantasyName = self::DEFAULT_FANTASY_NAME,
        string $userId = self::USER_ID
    ): UpdateCompanyInputDto
    {
        return new UpdateCompanyInputDto(
            $companyId,
            $fantasyName,
            $userId
        );
    }

    public static function withNonExistingId(): UpdateCompanyInputDto
    {
        return self::withValidData(companyId: self::NON_EXISTING_ID);
    }

    public static function withInvalidId(): UpdateCompanyInputDto
    {
        return self::withValidData(companyId: self::INVALID_ID);
    }

    public static function withEmptyId(): UpdateCompanyInputDto
    {
        return self::withValidData(companyId: '');
    }

    public static function withEmptyFantasyName(): UpdateCompanyInputDto
    {
        return self::withValidData(fantasyName: '');
    }
}