<?php

declare(strict_types=1);

namespace App\Domain\Event;

class ShoppingCartCancelled extends AbstractDomainEvent
{
    public function __construct(
        private readonly string $cartId,
        private readonly string $ownerId,
    ) {
        parent::__construct();
    }

    public function cartId(): string
    {
        return $this->cartId;
    }

    public function ownerId(): string
    {
        return $this->ownerId;
    }
}
