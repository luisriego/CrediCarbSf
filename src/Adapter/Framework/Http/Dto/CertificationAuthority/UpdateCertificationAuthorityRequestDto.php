<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\CertificationAuthority;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class UpdateCertificationAuthorityRequestDto implements RequestDto
{
    public ?string $name;
    public ?string $website;

    public function __construct(Request $request)
    {
        $this->name = $request->request->get('name');
        $this->website = $request->request->get('website');
    }
}
