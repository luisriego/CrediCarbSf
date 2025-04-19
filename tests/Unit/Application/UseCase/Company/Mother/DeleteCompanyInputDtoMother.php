<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\UseCase\Company\Mother;

use App\Application\UseCase\Company\DeleteCompanyService\Dto\DeleteCompanyInputDto;
use App\Tests\Unit\Domain\Model\Mother\CompanyMother;
use App\Tests\Unit\Domain\Model\Mother\UserMother;

final class DeleteCompanyInputDtoMother
{
    private const DEFAULT_COMPANY_ID = 'cbf070cc-80d2-4bfe-9fa6-a96b97ffb0da';
    private const DEFAULT_USER_ID = 'a5b6c15d-fa92-47bc-8289-9db382ae0123';
    private const NON_EXISTING_ID = 'e7b6c15d-fa92-47bc-8289-9db382ae0378';

    public static function create(
        ?string $companyId = null,
        ?string $userId = null
    ): DeleteCompanyInputDto {
        return DeleteCompanyInputDto::create(
            $companyId ?? self::DEFAULT_COMPANY_ID,
            $userId ?? self::DEFAULT_USER_ID
        );
    }

    public static function fromCompany(string $companyId, string $userId): DeleteCompanyInputDto
    {
        return self::create($companyId, $userId);
    }

    public static function withValidData(): DeleteCompanyInputDto
    {
        return self::create();
    }

    public static function withNonExistingId(): DeleteCompanyInputDto
    {
        return self::create(self::NON_EXISTING_ID);
    }

    public static function withDifferentUserId(string $userId): DeleteCompanyInputDto
    {
        return self::create(null, $userId);
    }
}