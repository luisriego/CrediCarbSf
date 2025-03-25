<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\ShoppingCart;

interface ShoppingCartRepositoryInterface
{
    public function add(ShoppingCart $shoppingCart, bool $flush);

    public function save(ShoppingCart $shoppingCart, bool $flush);

    public function remove(ShoppingCart $shoppingCart, bool $flush);

    public function findOneByIdOrFail(string $id): ShoppingCart;

    public function findOwnerById(string $ownerId): ?ShoppingCart;

    public function findActiveCartForCompanyOwnerOrFail(string $companyId): ?ShoppingCart;

    public function findOneByOwnerIdOrFail(string $ownerId): ResourceNotFoundException|ShoppingCart;

    public function findFirst(): ?ShoppingCart;
}
