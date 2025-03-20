<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Model\Discount;
use App\Domain\Model\ShoppingCart;

interface ShoppingCartWorkflowInterface
{
    public function canCheckout(ShoppingCart $cart): bool;

    public function checkout(ShoppingCart $cart, ?Discount $discount = null, ?TaxCalculator $taxCalculator = null): void;

    public function canCancel(ShoppingCart $cart): bool;

    public function cancel(ShoppingCart $cart): void;

    public function getAvailableTransitions(ShoppingCart $cart): array;
}
