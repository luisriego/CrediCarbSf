<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Company;
use App\Domain\Model\Discount;
use App\Domain\Model\Project;
use App\Domain\Model\ShoppingCart;
use App\Domain\Model\ShoppingCartItem;
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
        $shoppingCart->calculateTax(0.1);

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
        $shoppingCart->calculateTax(0.1);

        $this->assertEquals('0.00', $shoppingCart->getTotal());
        $this->assertEquals('0.00', $shoppingCart->getTax());
    }

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
        $shoppingCart->calculateTax(0.2);

        $this->assertEquals('50.00', $shoppingCart->getTotal());
        $this->assertEquals('10.00', $shoppingCart->getTax());
    }

    /**
     * @test
     */
    public function shouldHandleNegativeItemPrice(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $item = $this->createMock(ShoppingCartItem::class);
        $item->method('getTotalPrice')->willReturn('-10.00');

        $shoppingCart->addItem($item);

        $shoppingCart->calculateTotal();
        $shoppingCart->calculateTax(0.1);

        $this->assertEquals('0.00', $shoppingCart->getTotal());
        $this->assertEquals('0.00', $shoppingCart->getTax());
    }

    /**
     * @test
     */
    public function shouldHandleZeroTaxRate(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $item = $this->createMock(ShoppingCartItem::class);
        $item->method('getTotalPrice')->willReturn('100.00');

        $shoppingCart->addItem($item);

        $shoppingCart->calculateTotal();
        $shoppingCart->calculateTax(0.0);

        $this->assertEquals('100.00', $shoppingCart->getTotal());
        $this->assertEquals('0.00', $shoppingCart->getTax());
    }

    /**
     * @test
     */
    public function shouldHandleHighTaxRate(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $item = $this->createMock(ShoppingCartItem::class);
        $item->method('getTotalPrice')->willReturn('100.00');

        $shoppingCart->addItem($item);

        $shoppingCart->calculateTotal();
        $shoppingCart->calculateTax(1.0);

        $this->assertEquals('100.00', $shoppingCart->getTotal());
        $this->assertEquals('100.00', $shoppingCart->getTax());
    }

    /**
     * @test
     */
    public function shouldApplyDiscountCorrectly(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $item = $this->createMock(ShoppingCartItem::class);
        $item->method('getTotalPrice')->willReturn('100.00');

        $discount = $this->createMock(Discount::class);
        $discount->method('applyToAmount')->willReturn(90.00);
        $discount->method('getTargetProject')->willReturn(null);

        $shoppingCart->addItem($item);
        $shoppingCart->calculateTotal($discount);

        $this->assertEquals('90.00', $shoppingCart->getTotal());
    }

    /**
     * @test
     */
    public function shouldHandleDiscountOnSpecificProject(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn('project1');

        $item = $this->createMock(ShoppingCartItem::class);
        $item->method('getTotalPrice')->willReturn('100.00');
        $item->method('getProject')->willReturn($project);

        $discount = $this->createMock(Discount::class);
        $discount->method('applyToAmount')->willReturn(90.00);
        $discount->method('getTargetProject')->willReturn($project);

        $shoppingCart->addItem($item);
        $shoppingCart->calculateTotal($discount);

        $this->assertEquals('90.00', $shoppingCart->getTotal());
    }

    /**
     * @test
     */
    public function shouldNotApplyDiscountToDifferentProject(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $project1 = $this->createMock(Project::class);
        $project1->method('getId')->willReturn('project1');

        $project2 = $this->createMock(Project::class);
        $project2->method('getId')->willReturn('project2');

        $item = $this->createMock(ShoppingCartItem::class);
        $item->method('getTotalPrice')->willReturn('100.00');
        $item->method('getProject')->willReturn($project1);

        $discount = $this->createMock(Discount::class);
        $discount->method('applyToAmount')->willReturn(90.00);
        $discount->method('getTargetProject')->willReturn($project2);

        $shoppingCart->addItem($item);
        $shoppingCart->calculateTotal($discount);

        $this->assertEquals('100.00', $shoppingCart->getTotal());
    }

    /**
     * @test
     */
    public function shouldHandleMultipleItemsWithDiscount(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $item1 = $this->createMock(ShoppingCartItem::class);
        $item1->method('getTotalPrice')->willReturn('50.00');

        $item2 = $this->createMock(ShoppingCartItem::class);
        $item2->method('getTotalPrice')->willReturn('50.00');

        $discount = $this->createMock(Discount::class);
        $discount->method('applyToAmount')->willReturn(90.00);
        $discount->method('getTargetProject')->willReturn(null);

        $shoppingCart->addItem($item1);
        $shoppingCart->addItem($item2);
        $shoppingCart->calculateTotal($discount);

        $this->assertEquals('90.00', $shoppingCart->getTotal());
    }

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
        $shoppingCart->calculateTax(0.1);

        $this->assertEquals('35.00', $shoppingCart->getTotal());
        $this->assertEquals('3.50', $shoppingCart->getTax());

        $shoppingCart->removeItem($item1);
        $shoppingCart->calculateTax(0.1); // Recalculate tax after removing the item

        $this->assertEquals('15.00', $shoppingCart->getTotal());
        $this->assertEquals('1.50', $shoppingCart->getTax());
    }

    /**
     * @test
     */
    public function shouldNotAllowNegativeTotalWithExcessiveDiscount(): void
    {
        $company = $this->createMock(Company::class);
        $shoppingCart = new ShoppingCart($company);

        $item = $this->createMock(ShoppingCartItem::class);
        $item->method('getTotalPrice')->willReturn('50.00');

        $discount = $this->createMock(Discount::class);
        $discount->method('applyToAmount')->willReturn(-10.00);
        $discount->method('getTargetProject')->willReturn(null);

        $shoppingCart->addItem($item);
        $shoppingCart->calculateTotal($discount);

        $this->assertEquals('0.00', $shoppingCart->getTotal());
    }
}
