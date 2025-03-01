<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\CheckoutShoppingCart;

use App\Application\UseCase\ShoppingCart\CheckoutShoppingCart\Dto\CheckoutOutputDto;
use App\Application\UseCase\User\UserFinder\UserFinder;
use App\Domain\Exception\ShoppingCart\EmptyCartException;
use App\Domain\Exception\ShoppingCart\InsufficientStockException;
use App\Domain\Repository\ShoppingCartRepositoryInterface;

final readonly class CheckoutShoppingCartService
{
    public function __construct(
        private ShoppingCartRepositoryInterface $repository,
        private UserFinder                      $userFinder
    ) {
    }

    public function handle(): CheckoutOutputDto
    {
        $user = $this->userFinder->getCurrentUser();
        $shoppingCart = $this->repository->findOneByOwnerIdOrFail($user->getCompany()->getId());

        if ($shoppingCart->getItems()->isEmpty()) {
            throw new EmptyCartException();
        }

        $this->validateStock($shoppingCart);
        $shoppingCart->checkout();
        $this->repository->save($shoppingCart, true);

        return CheckoutOutputDto::fromEntity($shoppingCart);
    }

    private function validateStock($shoppingCart): void
    {
        foreach ($shoppingCart->getItems() as $item) {
            if ($item->getQuantity() > $item->getProject()->getQuantity()) {
                throw new InsufficientStockException($item->getProject()->getName());
            }
        }
    }
}