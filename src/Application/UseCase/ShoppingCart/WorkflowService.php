<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart;

use App\Domain\Model\Discount;
use App\Domain\Model\ShoppingCart;
use App\Domain\Services\ShoppingCartWorkflowInterface;
use App\Domain\Services\TaxCalculator;
use Symfony\Component\Workflow\Registry;

class WorkflowService implements ShoppingCartWorkflowInterface
{
    private Registry $workflowRegistry;
    private TaxCalculator $defaultTaxCalculator;

    public function __construct(Registry $workflowRegistry, float $taxRate, TaxCalculator $defaultTaxCalculator)
    {
        $this->workflowRegistry = $workflowRegistry;
        $this->defaultTaxCalculator = $defaultTaxCalculator;
    }

    public function canCheckout(ShoppingCart $cart): bool
    {
        $workflow = $this->workflowRegistry->get($cart, 'shopping_cart');
        return $workflow->can($cart, 'checkout') && !$cart->getItems()->isEmpty();
    }

    public function checkout(ShoppingCart $cart, ?Discount $discount = null, ?TaxCalculator $taxCalculator = null): void
    {
        $cart->checkout($discount);
        
        $cart->calculateTaxWithCalculator(
            $taxCalculator ?? $this->defaultTaxCalculator
        );
        
        // Solo nos encargamos de la transición de estado
        $workflow = $this->workflowRegistry->get($cart, 'shopping_cart');
        $workflow->apply($cart, 'checkout');
    }

    public function canCancel(ShoppingCart $cart): bool
    {
        $workflow = $this->workflowRegistry->get($cart, 'shopping_cart');
        return $workflow->can($cart, 'cancel');
    }

    public function cancel(ShoppingCart $cart): void
    {
        if (!$this->canCancel($cart)) {
            throw new \LogicException('No se puede cancelar el carrito en su estado actual');
        }
        
        // Llamamos a la lógica de negocio en el dominio
        $cart->cancel();
        
        // Solo nos encargamos de la transición de estado
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