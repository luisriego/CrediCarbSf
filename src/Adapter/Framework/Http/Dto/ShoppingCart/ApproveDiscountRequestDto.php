<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\ShoppingCart;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class ApproveDiscountRequestDto implements RequestDto
{
    public string $discountId;

    public function __construct(Request $request)
    {
        $this->discountId = $request->get('discountId');
    }
}
