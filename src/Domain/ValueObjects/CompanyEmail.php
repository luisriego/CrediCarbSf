<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use App\Domain\Exception\InvalidArgumentException;

final class CompanyEmail extends Email
{
    // I'm thiking in get the user email termination and and 
    // defined it as domain permitted
    private const ALLOWED_DOMAINS = [
        'company.com',
        'business.com',
        'enterprise.com',
    ];

    private const FORBIDEN_DOMAINS = [
        'gmail.com',
        'yahoo.com',
        'hotmail.com',
    ];

    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->validateCompanyDomain($value);
    }

    private function validateCompanyDomain(string $email): void
    {
        $domain = substr(strrchr($email, "@"), 1);
        
        if (!in_array($domain, self::ALLOWED_DOMAINS)) {
            throw InvalidArgumentException::createFromMessage(
                sprintf('Email domain must be one of: %s', implode(', ', self::ALLOWED_DOMAINS))
            );
        }

        if (in_array($domain, self::FORBIDEN_DOMAINS)) {
            throw InvalidArgumentException::createFromMessage(
                sprintf('Email domain %s is not allowed', $domain)
            );
        }
    }
}