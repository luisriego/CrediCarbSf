<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\CheckoutShoppingCart;

use App\Application\UseCase\ShoppingCart\CheckoutShoppingCart\Dto\CheckoutOutputDto;
use App\Domain\Exception\ShoppingCart\EmptyCartException;
use App\Domain\Exception\ShoppingCart\InsufficientStockException;
use App\Domain\Exception\ShoppingCart\InvalidDiscountException;
use App\Domain\Model\ShoppingCart;
use App\Domain\Repository\DiscountRepositoryInterface;
use App\Domain\Repository\ShoppingCartRepositoryInterface;
use App\Domain\Service\ShoppingCartWorkflowInterface;

final readonly class CheckoutShoppingCartService
{
    public function __construct(
        private ShoppingCartRepositoryInterface $repository,
        private DiscountRepositoryInterface $discountRepository,
        private ShoppingCartWorkflowInterface $shoppingCartWorkflow,
    ) {}

    /**
     * @throws EmptyCartException
     * @throws InsufficientStockException
     * @throws InvalidDiscountException
     */
    public function handle(ShoppingCart $shoppingCart, ?string $discountCode = null): CheckoutOutputDto
    {
        $this->validateEmptyCart($shoppingCart);
        $this->validateStock($shoppingCart);

        $discount = $this->discountRepository->findOneByCode($discountCode) ?? null;
        $discount?->validateDiscount();

        $this->shoppingCartWorkflow->checkout($shoppingCart, $discount);

        $this->repository->save($shoppingCart, true);

        return CheckoutOutputDto::fromEntity($shoppingCart, $discount);
    }

    private function validateEmptyCart(ShoppingCart $shoppingCart): void
    {
        if ($shoppingCart->getItems()->isEmpty()) {
            throw new EmptyCartException();
        }
    }

    private function validateStock(ShoppingCart $shoppingCart): void
    {
        foreach ($shoppingCart->getItems() as $item) {
            if ($item->getQuantity() > $item->getProject()->getQuantity()) {
                throw new InsufficientStockException($item->getProject()->getName());
            }
        }
    }
}
