<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\ShoppingCartItem;

interface ShoppingCartItemRepositoryInterface
{
    public function add(ShoppingCartItem $shoppingCartItem, bool $flush): void;

    public function save(ShoppingCartItem $shoppingCartItem, bool $flush): void;

    public function remove(ShoppingCartItem $shoppingCartItem, bool $flush): void;

    public function findOneByIdOrFail(string $id): ShoppingCartItem;

    public function findByShoppingCartId(string $shoppingCartId): array;
}
