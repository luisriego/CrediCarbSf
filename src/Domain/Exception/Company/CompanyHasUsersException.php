<?php

declare(strict_types=1);

namespace App\Domain\Exception\Company;

use App\Domain\Exception\HttpException;

class CompanyHasUsersException extends HttpException
{
    public static function deleteFromMessage(): self
    {
        return new self('Cannot delete company with associated users');
    }
}
