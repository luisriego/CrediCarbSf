<?php

declare(strict_types=1);

namespace App\Domain\Exception\ShoppingCart;

use DomainException;

class EmptyCartException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Cannot checkout with an empty cart');
    }
}
