<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\UpdateItemQuantityInShoppingCart\Dto;

use App\Domain\Validation\Traits\AssertValidQuantityTrait;
use App\Domain\Validation\Traits\AssertValidUidTrait;

class UpdateItemQuantityInShoppingCartInputDto
{
    use AssertValidUidTrait;
    use AssertValidQuantityTrait;

    private const ARGS = [
        'shoppingCartId',
        'itemId',
        'quantity',
    ];

    public function __construct(
        public string $shoppingCartId,
        public string $itemId,
        public int $quantity,
    ) {
        $this->assertValidUid($shoppingCartId);
        $this->assertValidUid($itemId);
        $this->assertValidQuantity($quantity);
    }

    public static function create(string $shoppingCartId, string $itemId, int $quantity): self
    {
        return new static($shoppingCartId, $itemId, $quantity);
    }
}
