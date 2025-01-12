<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByIdService;

use App\Application\UseCase\Company\GetCompanyByIdService\Dto\GetCompanyByIdInputDto;
use App\Application\UseCase\Company\GetCompanyByIdService\Dto\GetCompanyByIdOutputDto;

class GetCompanyByIdService
{
    public function handle(GetCompanyByIdInputDto $inputDto): GetCompanyByIdOutputDto
    {
        return GetCompanyByIdOutputDto::create($inputDto->company);
    }
}
