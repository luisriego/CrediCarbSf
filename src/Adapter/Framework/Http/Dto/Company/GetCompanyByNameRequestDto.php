<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Company;

use App\Adapter\Framework\Http\Dto\RequestDto;
use App\Domain\ValueObject\CompanyName;
use Symfony\Component\HttpFoundation\Request;

class GetCompanyByNameRequestDto implements RequestDto
{
    public string $name;

    public function __construct(Request $request)
    {
        $name = $request->query->get('name');
        $this->name = CompanyName::fromString($name)->value();
    }
}
