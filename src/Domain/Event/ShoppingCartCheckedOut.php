<?php

declare(strict_types=1);

namespace App\Domain\Event;

class ShoppingCartCheckedOut extends AbstractDomainEvent
{
    public function __construct(
        private readonly string $cartId,
        private readonly string $total,
        private readonly string $tax,
        private readonly string $ownerId,
    ) {
        parent::__construct();
    }

    public function cartId(): string
    {
        return $this->cartId;
    }

    public function total(): string
    {
        return $this->total;
    }

    public function tax(): string
    {
        return $this->tax;
    }

    public function ownerId(): string
    {
        return $this->ownerId;
    }
}
