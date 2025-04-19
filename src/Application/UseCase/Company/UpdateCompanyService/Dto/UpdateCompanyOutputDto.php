<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\UpdateCompanyService\Dto;

use App\Domain\Model\Company;

readonly class UpdateCompanyOutputDto
{
    public function __construct(
        public array $company,
    ) {}

    public static function create(string $companyId): self
    {
        return new self(['id' => $companyId]);
    }
}
