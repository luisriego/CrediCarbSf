<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\AddUserToCompanyService\Dto;

use App\Domain\Validation\Traits\AssertValidUidTrait;

class AddUserToCompanyInputDto
{
    use AssertValidUidTrait;

    public function __construct(
        public string $companyId,
        public string $userId,
    ) {
        $this->assertValidUid($companyId);
        $this->assertValidUid($userId);
    }

    public static function create(string $companyId, string $userId): self
    {
        return new self($companyId, $userId);
    }
}