<?php

declare(strict_types=1);

namespace App\Domain\Exception\ShoppingCart;

use App\Domain\Exception\HttpException;

use function sprintf;

class InvalidDiscountException extends HttpException
{
    public static function createWithMessage(string $message): self
    {
        return new self(400, $message);
    }

    public static function createFromCode(string $code): self
    {
        return new self(400, sprintf('Invalid discount code: %s', $code));
    }
}
