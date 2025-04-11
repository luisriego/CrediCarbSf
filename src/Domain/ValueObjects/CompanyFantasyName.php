<?<?php 

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final class CompanyFantasyName extends FantasyName
{
    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}