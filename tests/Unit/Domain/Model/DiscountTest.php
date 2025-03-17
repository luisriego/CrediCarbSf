<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Discount;
use App\Domain\Model\Project;
use App\Domain\Model\User;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use ReflectionClass;

class DiscountTest extends TestCase
{
    /**
     * @throws RandomException
     */
    public function testApplyToAmount(): void
    {
        $user = $this->createMock(User::class);
        $amount = 2050; // 20.5%
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with a percentage discount
        $discount = Discount::createWithAmountAndExpirationDate($user, $amount, $expiresAt);
        $this->assertEquals(79.5, $discount->applyToAmount(100));

        $amount = 1369; // 13.69 units
        // Test with a fixed amount discount
        $discount = Discount::createWithAmountAndExpirationDateNotPercentage($user, $amount, $expiresAt);
        $this->assertEquals(86.31, $discount->applyToAmount(100));
    }

    /**
     * @throws RandomException
     */
    public function testZeroDiscountAmount(): void
    {
        $user = $this->createMock(User::class);
        $amount = 0; // 0%
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with a zero percentage discount
        $discount = Discount::createWithAmountAndExpirationDate($user, $amount, $expiresAt);
        $this->assertEquals(100, $discount->applyToAmount(100));

        // Test with a zero fixed amount discount
        $discount = Discount::createWithAmountAndExpirationDateNotPercentage($user, $amount, $expiresAt);
        $this->assertEquals(100, $discount->applyToAmount(100));
    }

    /**
     * @throws RandomException
     */
    public function testFullDiscountAmount(): void
    {
        $user = $this->createMock(User::class);
        $amount = 10000; // 100%
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with a full percentage discount
        $discount = Discount::createWithAmountAndExpirationDate($user, $amount, $expiresAt);
        $this->assertEquals(0, $discount->applyToAmount(100));
    }

    public function testDiscountAmountGreaterThanOriginal(): void
    {
        $user = $this->createMock(User::class);
        $amount = 15000; // 150 units
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with a fixed amount discount greater than the original amount
        $discount = Discount::createWithAmountAndExpirationDateNotPercentage($user, $amount, $expiresAt);
        $this->assertEquals(0, $discount->applyToAmount(100));
    }

    public function testNegativeDiscountAmount(): void
    {
        $user = $this->createMock(User::class);
        $amount = -500; // -50 units
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with a negative fixed amount discount
        $this->expectException(InvalidArgumentException::class);
        Discount::createWithAmountAndExpirationDateNotPercentage($user, $amount, $expiresAt);
    }

    /**
     * @throws RandomException
     */
    public function testValidExpirationDate(): void
    {
        $user = $this->createMock(User::class);
        $amount = 10;

        // Test with a valid expiration date (+2 days)
        $validExpiresAt = new DateTimeImmutable('+2 days');
        $discount = Discount::createWithAmountAndExpirationDate($user, $amount, $validExpiresAt);
        $this->assertInstanceOf(Discount::class, $discount);
    }

    /**
     * @throws RandomException
     */
    public function testInvalidExpirationDate(): void
    {
        $user = $this->createMock(User::class);
        $amount = 10;

        // Test with an invalid expiration date (today)
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The expiration date must be at least +1 day from today.');
        $invalidExpiresAt = new DateTimeImmutable();
        Discount::createWithAmountAndExpirationDate($user, $amount, $invalidExpiresAt);
    }

    /**
     * @throws RandomException
     */
    public function testZeroOriginalAmount(): void
    {
        $user = $this->createMock(User::class);
        $amount = 2050; // 20.5%
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with a percentage discount
        $discount = Discount::createWithAmountAndExpirationDate($user, $amount, $expiresAt);
        $this->assertEquals(0, $discount->applyToAmount(0));

        $amount = 1369; // 13.69 units
        // Test with a fixed amount discount
        $discount = Discount::createWithAmountAndExpirationDateNotPercentage($user, $amount, $expiresAt);
        $this->assertEquals(0, $discount->applyToAmount(0));
    }

    public function testDiscountAmountEqualToOriginal(): void
    {
        $user = $this->createMock(User::class);
        $amount = 10000; // 100 units
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with a fixed amount discount equal to the original amount
        $discount = Discount::createWithAmountAndExpirationDateNotPercentage($user, $amount, $expiresAt);
        $this->assertEquals(0, $discount->applyToAmount(100));
    }

    /**
     * @throws RandomException
     */
    public function testZeroDiscountAmountAndZeroOriginalAmount(): void
    {
        $user = $this->createMock(User::class);
        $amount = 0; // 0%
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with a zero percentage discount
        $discount = Discount::createWithAmountAndExpirationDate($user, $amount, $expiresAt);
        $this->assertEquals(0, $discount->applyToAmount(0));

        // Test with a zero fixed amount discount
        $discount = Discount::createWithAmountAndExpirationDateNotPercentage($user, $amount, $expiresAt);
        $this->assertEquals(0, $discount->applyToAmount(0));
    }

    /**
     * @throws RandomException
     */
    public function testExpiredDiscount(): void
    {
        $user = $this->createMock(User::class);
        $amount = 2050; // 20.5%
        $validExpiresAt = new DateTimeImmutable('+2 days');

        // Create a discount with a valid expiration date
        $discount = Discount::createWithAmountAndExpirationDate($user, $amount, $validExpiresAt);

        // Manually set the expiration date to an expired date
        $reflection = new ReflectionClass($discount);
        $property = $reflection->getProperty('expiresAt');
        $property->setAccessible(true);
        $property->setValue($discount, new DateTimeImmutable('-1 day'));

        $this->assertFalse($discount->isValid());
    }

    /**
     * @throws RandomException
     */
    public function testInactiveDiscount(): void
    {
        $user = $this->createMock(User::class);
        $amount = 2050; // 20.5%
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with an inactive discount
        $discount = Discount::createWithAmountAndExpirationDate($user, $amount, $expiresAt);
        //        $discount->deactivate();
        $this->assertFalse($discount->isValid());
    }

    /**
     * @throws RandomException
     */
    public function testItemSpecificDiscount(): void
    {
        $user = $this->createMock(User::class);
        $project = $this->createMock(Project::class);
        $amount = 2050; // 20.5%
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with an item-specific discount
        $discount = Discount::createWithAmountAndExpirationDate($user, $amount, $expiresAt);
        $discount->setTargetProject($project);
        $this->assertEquals($project, $discount->getTargetProject());
    }

    /**
     * @throws RandomException
     */
    public function testDiscountNotApplicableToAnyItems(): void
    {
        $user = $this->createMock(User::class);
        $project = $this->createMock(Project::class);
        $amount = 2050; // 20.5%
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with a discount that does not apply to any items
        $discount = Discount::createWithProjectToApply($user, $amount, $expiresAt, $project);
        $this->assertEquals(100, $discount->applyToAmount(100));
    }

    /**
     * @throws RandomException
     */
    public function testDiscountApplicableToTotalAmount(): void
    {
        $user = $this->createMock(User::class);
        $amount = 2050; // 20.5%
        $expiresAt = new DateTimeImmutable('+2 days');

        // Test with a discount that applies to the total amount
        $discount = Discount::createWithAmountAndExpirationDate($user, $amount, $expiresAt);
        $this->assertEquals(79.5, $discount->applyToAmount(100));
    }
}
