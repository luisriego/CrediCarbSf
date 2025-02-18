<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\RemoveItemFromShoppingCartService\Dto;

class RemoveItemFromShoppingCartOutputDto
{
    public function __construct(
        public string $shoppingCartId,
        public array $itemIds,
        public array $remainingItems,
    ) {}

    public static function create(string $shoppingCartId, array $itemIds, array $remainingItems): self
    {
        return new static($shoppingCartId, $itemIds, $remainingItems);
    }
}
