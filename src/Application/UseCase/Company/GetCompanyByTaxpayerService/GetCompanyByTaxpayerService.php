<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByTaxpayerService;

use App\Application\UseCase\Company\GetCompanyByTaxpayerService\Dto\GetCompanyByTaxpayerInputDto;
use App\Application\UseCase\Company\GetCompanyByTaxpayerService\Dto\GetCompanyByTaxpayerOutputDto;

class GetCompanyByTaxpayerService
{
    public function handle(GetCompanyByTaxpayerInputDto $inputDto): GetCompanyByTaxpayerOutputDto
    {
        return GetCompanyByTaxpayerOutputDto::create($inputDto->company);
    }
}
