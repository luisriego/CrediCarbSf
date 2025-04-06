<?php

namespace App\Domain\Factory;

use App\Domain\Model\Company;

class CompanyFactory
{
    public function create(string $taxpayer, string $fantasyName): Company
    {
        return Company::create(
            $taxpayer,
            $fantasyName,
        );
    }
}