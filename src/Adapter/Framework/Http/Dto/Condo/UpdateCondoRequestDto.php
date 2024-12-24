<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Condo;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class UpdateCondoRequestDto implements RequestDto
{
    public readonly ?string $id;
    public readonly ?string $taxpayer;
    public readonly ?string $fantasyName;
    public readonly array $keys;

    public function __construct(Request $request)
    {
        $this->id = $request->request->get('id');
        $this->taxpayer = $request->request->get('taxpayer');
        $this->fantasyName = $request->request->get('fantasyName');
        $this->keys = \array_keys($request->request->all());
    }
}
