<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Company;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class UpdateCompanyRequestDto implements RequestDto
{
    public ?string $fantasyName;

    public function __construct(Request $request)
    {
        $this->fantasyName = $request->request->get('fantasyName');
    }
}
