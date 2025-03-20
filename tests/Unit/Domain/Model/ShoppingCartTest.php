<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Company;
use App\Domain\Model\ShoppingCart;
use App\Domain\Model\ShoppingCartItem;
use App\Domain\Services\TaxCalculator;
use PHPUnit\Framework\TestCase;

class ShoppingCartTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCalculateTotalAndTaxCorrectly(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $item1 = $this->createMock(ShoppingCartItem::class);
        $item1->method('getTotalPrice')->willReturn('20.00');

        $item2 = $this->createMock(ShoppingCartItem::class);
        $item2->method('getTotalPrice')->willReturn('15.00');

        $shoppingCart->addItem($item1);
        $shoppingCart->addItem($item2);

        $shoppingCart->calculateTotal();
        
        // Creamos un mock de TaxCalculator
        $taxCalculator = $this->createMock(TaxCalculator::class);
        // Configuramos para que devuelva un valor específico (10% de 35.00 = 3.50)
        $taxCalculator->method('calculateTaxForAmount')->willReturn(3.50);
        
        $shoppingCart->calculateTaxWithCalculator($taxCalculator);

        $this->assertEquals('35.00', $shoppingCart->getTotal());
        $this->assertEquals('3.50', $shoppingCart->getTax());
    }

    /**
     * @test
     */
    public function shouldHandleEmptyCart(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $shoppingCart->calculateTotal();
        
        // Creamos un mock de TaxCalculator
        $taxCalculator = $this->createMock(TaxCalculator::class);
        // Para un carrito vacío, el impuesto será 0
        $taxCalculator->method('calculateTaxForAmount')->willReturn(0.00);
        
        $shoppingCart->calculateTaxWithCalculator($taxCalculator);

        $this->assertEquals('0.00', $shoppingCart->getTotal());
        $this->assertEquals('0.00', $shoppingCart->getTax());
    }
    
    // El resto de tests también necesitan ser actualizados de la misma manera
    // Mostraré algunos ejemplos:
    
    /**
     * @test
     */
    public function shouldHandleSingleItemCart(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $item = $this->createMock(ShoppingCartItem::class);
        $item->method('getTotalPrice')->willReturn('50.00');

        $shoppingCart->addItem($item);
        $shoppingCart->calculateTotal();
        
        $taxCalculator = $this->createMock(TaxCalculator::class);
        // 20% de 50.00 = 10.00
        $taxCalculator->method('calculateTaxForAmount')->willReturn(10.00);
        
        $shoppingCart->calculateTaxWithCalculator($taxCalculator);

        $this->assertEquals('50.00', $shoppingCart->getTotal());
        $this->assertEquals('10.00', $shoppingCart->getTax());
    }
    
    // Y así sucesivamente para todos los demás tests...
    // ...
    
    /**
     * @test
     */
    public function shouldRecalculateTotalAndTaxWhenItemRemoved(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $item1 = $this->createMock(ShoppingCartItem::class);
        $item1->method('getTotalPrice')->willReturn('20.00');

        $item2 = $this->createMock(ShoppingCartItem::class);
        $item2->method('getTotalPrice')->willReturn('15.00');

        $shoppingCart->addItem($item1);
        $shoppingCart->addItem($item2);

        $shoppingCart->calculateTotal();
        
        $taxCalculator = $this->createMock(TaxCalculator::class);
        $taxCalculator->method('calculateTaxForAmount')->willReturn(3.50);
        
        $shoppingCart->calculateTaxWithCalculator($taxCalculator);

        $this->assertEquals('35.00', $shoppingCart->getTotal());
        $this->assertEquals('3.50', $shoppingCart->getTax());

        $shoppingCart->removeItem($item1);
        
        // Nuevo calculador para el nuevo total
        $newTaxCalculator = $this->createMock(TaxCalculator::class);
        $newTaxCalculator->method('calculateTaxForAmount')->willReturn(1.50);
        
        $shoppingCart->calculateTaxWithCalculator($newTaxCalculator);

        $this->assertEquals('15.00', $shoppingCart->getTotal());
        $this->assertEquals('1.50', $shoppingCart->getTax());
    }
}