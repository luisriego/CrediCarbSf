<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\RemoveItemFromShoppingCartService;

use App\Application\UseCase\ShoppingCart\RemoveItemFromShoppingCartService\Dto\RemoveItemFromShoppingCartInputDto;
use App\Application\UseCase\ShoppingCart\RemoveItemFromShoppingCartService\Dto\RemoveItemFromShoppingCartOutputDto;
use App\Domain\Repository\ShoppingCartItemRepositoryInterface;
use App\Domain\Repository\ShoppingCartRepositoryInterface;

final class RemoveItemFromShoppingCartService
{
    public function __construct(
        private readonly ShoppingCartItemRepositoryInterface $shoppingCartItemRepository,
        private readonly ShoppingCartRepositoryInterface $shoppingCartRepository,
    ) {}

    public function handle(RemoveItemFromShoppingCartInputDto $inputDto): ?RemoveItemFromShoppingCartOutputDto
    {
        $shoppingCartItem = $this->shoppingCartItemRepository->findOneByIdOrFail($inputDto->shoppingCartItemId);
        $shoppingCart = $shoppingCartItem->getShoppingCart();

        $shoppingCart->removeItem($shoppingCartItem);
        $this->shoppingCartItemRepository->remove($shoppingCartItem, true);

        if ($shoppingCart->getItems()->isEmpty()) {
            $this->shoppingCartRepository->remove($shoppingCart, true);

            return null;
        }

        $this->shoppingCartRepository->save($shoppingCart, true);

        $remainingItems = $shoppingCart->getItems()->map(fn ($item) => $item->toArray())->toArray();

        return RemoveItemFromShoppingCartOutputDto::create(
            $shoppingCartItem->getId(),
            $shoppingCart->getItems()->map(fn ($item) => $item->getId())->toArray(),
            $remainingItems,
        );
    }
}
