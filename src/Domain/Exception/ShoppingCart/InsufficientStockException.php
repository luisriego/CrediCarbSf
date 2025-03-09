<?php

declare(strict_types=1);

namespace App\Domain\Exception\ShoppingCart;

use DomainException;

use function sprintf;

class InsufficientStockException extends DomainException
{
    public function __construct(string $productName)
    {
        parent::__construct(sprintf('Insufficient stock for product %s', $productName));
    }
}
