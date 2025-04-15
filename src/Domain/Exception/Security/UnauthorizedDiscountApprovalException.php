<?php

declare(strict_types=1);

namespace App\Domain\Exception\Security;

use App\Domain\Exception\HttpException;
use App\Domain\Model\Discount;
use App\Domain\Model\User;

use function sprintf;

class UnauthorizedDiscountApprovalException extends HttpException
{
    public static function createFromCodeAndUser(User $approver, Discount $discount): self
    {
        return new self(403, sprintf('Unauthorized discount code %d from approver %s.', $discount->code(), $approver->getName()));
    }
}
