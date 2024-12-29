<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Company;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class CreateCompanyRequestDto implements RequestDto
{
    public string $fantasyName;
    public string $taxpayer;

    public function __construct(Request $request)
    {
        $this->fantasyName = $request->get('fantasyName');
        $this->taxpayer = $request->get('taxpayer');
    }
}
