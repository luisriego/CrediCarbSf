<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Condo;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class CreateCondoRequestDto implements RequestDto
{
    public string $taxpayer;
    public string $fantasyName;

    public function __construct(Request $request)
    {
        $this->taxpayer = $request->request->get('taxpayer');
        $this->fantasyName = $request->request->get('fantasyName');
    }
}
