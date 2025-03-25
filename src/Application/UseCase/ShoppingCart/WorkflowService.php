<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart;

use App\Domain\Exception\ShoppingCart\ShoppingCartWorkflowException;
use App\Domain\Model\Discount;
use App\Domain\Model\ShoppingCart;
use App\Domain\Services\ShoppingCartWorkflowInterface;
use App\Domain\Services\TaxCalculator;
use LogicException;
use Symfony\Component\Workflow\Registry;

class WorkflowService implements ShoppingCartWorkflowInterface
{
    private Registry $workflowRegistry;
    private TaxCalculator $defaultTaxCalculator;

    public function __construct(Registry $workflowRegistry, TaxCalculator $defaultTaxCalculator)
    {
        $this->workflowRegistry = $workflowRegistry;
        $this->defaultTaxCalculator = $defaultTaxCalculator;
    }

    public function canCheckout(ShoppingCart $cart): bool
    {
        $workflow = $this->workflowRegistry->get($cart, 'shopping_cart');

        return $workflow->can($cart, 'checkout') && !$cart->getItems()->isEmpty();
    }

    /**
     * @throws ShoppingCartWorkflowException
     */
    public function checkout(ShoppingCart $cart, ?Discount $discount = null): void
    {
        $cart->checkout($discount);

        $workflow = $this->workflowRegistry->get($cart, 'shopping_cart');
        $workflow->apply($cart, 'checkout');
    }

    public function canCancel(ShoppingCart $cart): bool
    {
        return $this->workflowRegistry->get($cart, 'shopping_cart')->can($cart, 'cancel');
    }

    public function cancel(ShoppingCart $cart): void
    {
        if (!$this->canCancel($cart)) {
            throw new LogicException('The shopping cart cannot be canceled in its current state.');
        }

        $cart->cancel();

        $workflow = $this->workflowRegistry->get($cart, 'shopping_cart');
        $workflow->apply($cart, 'cancel');
    }

    public function getAvailableTransitions(ShoppingCart $cart): array
    {
        $workflow = $this->workflowRegistry->get($cart, 'shopping_cart');
        $transitions = [];

        foreach ($workflow->getEnabledTransitions($cart) as $transition) {
            $transitions[] = $transition->getName();
        }

        return $transitions;
    }
}
