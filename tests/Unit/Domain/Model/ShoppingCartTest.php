<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Company;
use App\Domain\Model\ShoppingCart;
use App\Domain\Model\ShoppingCartItem;
use PHPUnit\Framework\TestCase;

class ShoppingCartTest extends TestCase
{
    /** @test */
    public function shouldCalculateTotalAndTaxCorrectly(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $item1 = $this->createMock(ShoppingCartItem::class);
        $item1->method('getPrice')->willReturn('10.00');
        $item1->method('getQuantity')->willReturn(2);

        $item2 = $this->createMock(ShoppingCartItem::class);
        $item2->method('getPrice')->willReturn('5.00');
        $item2->method('getQuantity')->willReturn(3);

        $shoppingCart->addItem($item1);
        $shoppingCart->addItem($item2);

        $shoppingCart->calculateTotal();

        $this->assertEquals('35.00', $shoppingCart->getTotal());
        $this->assertEquals('3.50', $shoppingCart->getTax());
    }
}