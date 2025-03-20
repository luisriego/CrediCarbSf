<?php

declare(strict_types=1);

namespace App\Domain\Services;

class TaxCalculator
{
    private float $taxRate;
    
    public function __construct(float $taxRate)
    {
        $this->taxRate = $taxRate;
    }
    
    public function calculateTax(float $amount): float
    {
        return $amount * $this->taxRate;
    }

    public function calculateTaxForAmount(float $amount): float
    {
        return $this->calculateTax($amount);
    }
}