<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\ShoppingCart;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class UpdateItemQuantityInShoppingCartRequestDto implements RequestDto
{
    public string $shoppingCartId;
    public string $itemId;
    public int $quantity;

    public function __construct(Request $request)
    {
        $this->shoppingCartId = $request->get('shoppingCartId');
        $this->itemId = $request->get('itemId');
        $this->quantity = $request->get('quantity');
    }
}
