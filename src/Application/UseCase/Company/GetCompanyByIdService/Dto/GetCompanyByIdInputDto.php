<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByIdService\Dto;

use App\Domain\Model\Company;
use App\Domain\Validation\Traits\AssertValidUidTrait;

class GetCompanyByIdInputDto
{
    use AssertValidUidTrait;

    public function __construct(
        public readonly Company $company,
    ) {
        $this->assertValidUid($this->company->getId());
    }

    public static function create(Company $company): self
    {
        return new static($company);
    }
}
