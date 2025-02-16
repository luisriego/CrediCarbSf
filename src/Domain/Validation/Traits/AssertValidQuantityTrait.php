<?php

declare(strict_types=1);

namespace App\Domain\Validation\Traits;

use App\Domain\Exception\InvalidArgumentException;

trait AssertValidQuantityTrait
{
    public function assertValidQuantity(int $quantity): void
    {
        if ($quantity <= 0) {
            throw InvalidArgumentException::createFromArgument('Quantity must be greater than 0');
        }
    }

    public function assertValidPrice(string $price): void
    {
        if ($price <= 0) {
            throw InvalidArgumentException::createFromArgument('Price must be greater than 0');
        }
    }
}
