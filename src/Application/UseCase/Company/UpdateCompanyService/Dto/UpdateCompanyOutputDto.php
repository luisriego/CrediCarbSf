<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\UpdateCompanyService\Dto;

use App\Domain\Model\Company;

class UpdateCompanyOutputDto
{
    public function __construct(
        public readonly Company $company,
    ) {}

    public static function create(Company $company): self
    {
        return new static($company);
    }
}
