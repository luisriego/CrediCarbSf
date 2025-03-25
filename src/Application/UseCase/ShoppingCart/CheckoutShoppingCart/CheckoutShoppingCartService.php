<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\CheckoutShoppingCart;

use App\Application\UseCase\ShoppingCart\CheckoutShoppingCart\Dto\CheckoutOutputDto;
use App\Domain\Exception\ShoppingCart\EmptyCartException;
use App\Domain\Exception\ShoppingCart\InsufficientStockException;
use App\Domain\Exception\ShoppingCart\InvalidDiscountException;
use App\Domain\Exception\ShoppingCart\ShoppingCartWorkflowException;
use App\Domain\Model\ShoppingCart;
use App\Domain\Repository\DiscountRepositoryInterface;
use App\Domain\Repository\ShoppingCartRepositoryInterface;
use App\Domain\Services\ShoppingCartWorkflowInterface;

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
     * @throws ShoppingCartWorkflowException|InvalidDiscountException
     */
    public function handle(ShoppingCart $shoppingCart, ?string $discountCode = null): CheckoutOutputDto
    {
        $this->validateEmptyCart($shoppingCart);
        $this->validateStock($shoppingCart);

        //        // Also here, a TDA to apply discount when exists
        //        // something like...
        //        // $this->applyDiscount(?string $discountCode = null);
        $discount = null;

        if ($discountCode !== null) {
            $discount = $this->discountRepository->findOneByCodeOrFail($discountCode);

            if (!$discount->isValid()) {
                throw InvalidDiscountException::createWithMessage('Invalid discount code');
            }
        }

        // make this in $this->shoppingCartWorkflow->checkout method
//        if (!$this->shoppingCartWorkflow->canCheckout($shoppingCart)) {
//            throw ShoppingCartWorkflowException::createWithMessage('The shopping cart cannot be checked out');
//        }

        // include a transaction around the checkout
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
