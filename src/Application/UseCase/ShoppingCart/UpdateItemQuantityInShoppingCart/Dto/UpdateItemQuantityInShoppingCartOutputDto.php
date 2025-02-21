<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\UpdateItemQuantityInShoppingCart\Dto;

class UpdateItemQuantityInShoppingCartOutputDto
{
    public function __construct(
        public string $shoppingCartId,
        public array $itemIds,
    ) {}

    public static function create(string $shoppingCartId, array $itemIds): self
    {
        return new static($shoppingCartId, $itemIds);
    }
}
