<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Company;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class AddUserToCompanyRequestDto implements RequestDto
{
    public string $userId;

    public function __construct(Request $request)
    {
        $this->userId = $request->get('userId');
    }
}
