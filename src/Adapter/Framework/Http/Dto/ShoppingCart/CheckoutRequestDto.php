<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\ShoppingCart;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class CheckoutRequestDto implements RequestDto
{
    public ?string $discountCode;

    public function __construct(Request $request)
    {
        $this->discountCode = $request->get('discount') ?? null;
    }
}
