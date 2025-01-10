<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Company;

use Symfony\Component\HttpFoundation\Request;

class DeleteCompanyRequestDto implements RequestDto
{
    public readonly ?string $id;

    public function __construct(Request $request)
    {
        $this->id = $request->attributes->get('id');
    }
}
