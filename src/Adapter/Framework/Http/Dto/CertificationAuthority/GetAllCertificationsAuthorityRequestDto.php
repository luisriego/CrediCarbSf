<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\CertificationAuthority;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class GetAllCertificationsAuthorityRequestDto implements RequestDto
{
    public function __construct(Request $request) {}
}
