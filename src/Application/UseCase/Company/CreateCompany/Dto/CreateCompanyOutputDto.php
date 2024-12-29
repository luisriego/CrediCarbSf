<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\CreateCompany\Dto;

class CreateCompanyOutputDto
{
    public string $companyId;

    public function __construct(string $companyId)
    {
        $this->companyId = $companyId;
    }
}
