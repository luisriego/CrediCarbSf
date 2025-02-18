<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\RemoveAllItemsFromShoppingCartService;

use App\Application\UseCase\ShoppingCart\RemoveAllItemsFromShoppingCartService\Dto\RemoveAllItemsFromShoppingCartInputDto;
use App\Domain\Model\ShoppingCart;
use App\Domain\Repository\ShoppingCartItemRepositoryInterface;
use App\Domain\Repository\ShoppingCartRepositoryInterface;

class RemoveAllItemsFromShoppingCartService
{
    public function __construct(
        //        private readonly ShoppingCartItemRepositoryInterface $shoppingCartItemRepository,
        private readonly ShoppingCartRepositoryInterface $shoppingCartRepository,
    ) {}

    public function handle(RemoveAllItemsFromShoppingCartInputDto $inputDto): void
    {
        /** @var ShoppingCart $shoppingCart */
        $shoppingCart = $this->shoppingCartRepository->findOneByIdOrFail($inputDto->shoppingCartId);

        $shoppingCart->removeAllItems();
        $this->shoppingCartRepository->remove($shoppingCart, true);
        //        $this->shoppingCartRepository->save($shoppingCart, true);
    }
}
