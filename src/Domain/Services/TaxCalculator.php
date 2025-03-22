<?php

declare(strict_types=1);

namespace App\Domain\Services;

class TaxCalculator
{
    public function calculateTax(float $amount): float
    {
        return $amount;
    }

    public function calculateTaxForAmount(float $amount): float
    {
        return $this->calculateTax($amount);
    }

    /**
     * Calculates tax rate based on complex business rules
     * Implement your business logic here.
     */
    public function determineTaxRate(array $context = []): float
    {
        // Here you can implement complex business logic to determine tax rate
        // based on product category, customer location, etc.
        return 0.21; // Default rate, replace with your domain logic
    }
}
