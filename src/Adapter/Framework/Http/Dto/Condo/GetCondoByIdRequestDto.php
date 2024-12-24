<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Condo;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class GetCondoByIdRequestDto implements RequestDto
{
    public readonly ?string $id;

    public function __construct(Request $request)
    {
        $this->id = $request->attributes->get('id');
    }
}
