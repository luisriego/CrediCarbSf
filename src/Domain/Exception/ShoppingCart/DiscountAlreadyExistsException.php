<?php

declare(strict_types=1);

namespace App\Domain\Exception\ShoppingCart;

use App\Domain\Exception\HttpException;

final class DiscountAlreadyExistsException extends HttpException
{
    public static function createRepeated(): self
    {
        return new self(400, 'Discount already exists');
    }
}
