<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Condo;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class ActivateCondoRequestDto implements RequestDto
{
    public readonly ?string $id;

    public function __construct(Request $request)
    {
        $this->id = $request->request->get('id');
    }
}
