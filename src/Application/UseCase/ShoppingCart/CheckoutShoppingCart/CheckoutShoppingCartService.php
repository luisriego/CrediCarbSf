<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\CheckoutShoppingCart;

use App\Application\UseCase\ShoppingCart\CheckoutShoppingCart\Dto\CheckoutOutputDto;
use App\Application\UseCase\User\UserFinder\UserFinder;
use App\Domain\Exception\ShoppingCart\EmptyCartException;
use App\Domain\Exception\ShoppingCart\InsufficientStockException;
use App\Domain\Exception\ShoppingCart\InvalidDiscountException;
use App\Domain\Exception\ShoppingCart\ShoppingCartWorkflowException;
use App\Domain\Model\ShoppingCart;
use App\Domain\Model\User;
use App\Domain\Repository\DiscountRepositoryInterface;
use App\Domain\Repository\ShoppingCartRepositoryInterface;
use App\Domain\Services\ShoppingCartWorkflowInterface;
use App\Domain\Services\TaxCalculator;

final readonly class CheckoutShoppingCartService
{
    public function __construct(
        private ShoppingCartRepositoryInterface $repository,
        private DiscountRepositoryInterface $discountRepository,
        private UserFinder $userFinder,
        private ShoppingCartWorkflowInterface $shoppingCartWorkflow,
        private TaxCalculator $taxCalculator,
    ) {}

    /**
     * @throws InvalidDiscountException
     * @throws EmptyCartException
     * @throws InsufficientStockException
     * @throws ShoppingCartWorkflowException
     */
    public function handle(User $user, ShoppingCart $shoppingCart, ?string $discountCode = null): CheckoutOutputDto
    {
        // Refactoring with TDA
        if ($shoppingCart->getItems()->isEmpty()) {
            throw new EmptyCartException();
        }

        // Also here, a TDA to apply discount when exists
        // something like...
        // $this->applyDiscount(?string $discountCode = null);
        $discount = null;

        if ($discountCode !== null) {
            $discount = $this->discountRepository->findOneByCodeOrFail($discountCode);

            if (!$discount->isValid()) {
                throw InvalidDiscountException::createWithMessage('Invalid discount code');
            }
        }

        $this->validateStock($shoppingCart);

        // make this in $this->shoppingCartWorkflow->checkout method
        if (!$this->shoppingCartWorkflow->canCheckout($shoppingCart)) {
            throw ShoppingCartWorkflowException::createWithMessage('The shopping cart cannot be checked out');
        }

        // include a transaction around the checkout
        $this->shoppingCartWorkflow->checkout($shoppingCart, $discount, $this->taxCalculator);

        $this->repository->save($shoppingCart, true);

        return CheckoutOutputDto::fromEntity($shoppingCart, $discount);
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
