<?php

declare(strict_types=1);

namespace App\Domain\Event;

class DiscountCodeApplied extends AbstractDomainEvent
{
    public function __construct(
        private readonly string $cartId,
        private readonly string $discountCode,
        private readonly float $discountAmount,
    ) {
        parent::__construct();
    }

    public function cartId(): string
    {
        return $this->cartId;
    }

    public function discountCode(): string
    {
        return $this->discountCode;
    }

    public function discountAmount(): float
    {
        return $this->discountAmount;
    }
}
