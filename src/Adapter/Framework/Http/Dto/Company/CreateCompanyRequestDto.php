<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Company;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class CreateCompanyRequestDto implements RequestDto
{
    public string $id;
    public string $taxpayer;
    public string $fantasyName;

    public function __construct(Request $request)
    {
        $this->id = $request->get('id');
        $this->taxpayer = $request->get('taxpayer');
        $this->fantasyName = $request->get('fantasyName');
    }
}
