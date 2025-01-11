<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\GetCompanyByNameService\Dto;

use App\Domain\Model\Company;

class GetCompanyByNameOutputDto
{

    private function __construct(public array $data) {}

    public static function create(array $arrayCompanies): self
    {
        $companies = array_map(function ($company) {
            return [
                'id' => $company->getId(),
                'fantasyName' => $company->getFantasyName(),
                'taxpayer' => $company->getTaxpayer(),
            ];
        }, $arrayCompanies);

        return new self($companies);
    }
}