<?php

declare(strict_types=1);

namespace App\Domain\Common;

enum UserRole: string
{
    case USER = 'ROLE_USER';
    case OPERATOR = 'ROLE_OPERATOR';
    case DISCOUNT_APPROVER = 'ROLE_DISCOUNT_APPROVER';
    case ADMIN = 'ROLE_ADMIN';
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
