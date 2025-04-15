<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Common\UserRole;
use App\Domain\Factory\DiscountFactory;
use App\Domain\Model\Discount;
use App\Domain\Model\Project;
use App\Domain\Model\User;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use ReflectionClass;
use ReflectionException;

class DiscountTest extends TestCase
{
    private DiscountFactory $factory;
    private User $user;
    private Discount $discount;
    private string $expiresAtString;
    private int $amount = 2050;

    /**
     * @throws Exception
     * @throws RandomException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new DiscountFactory();
        $this->user = $this->createMock(User::class);
        $expiresAt = new DateTimeImmutable('+2 days');
        $this->expiresAtString = $expiresAt->format('Y-m-d H:i:s');
        $this->discount = $this->factory->create($this->user, $this->amount, $this->expiresAtString);
    }

    /**
     * @throws RandomException
     */
    public function testApplyToAmount(): void
    {
        // This $amount = 2050;
        $this->assertEquals(79.5, $this->discount->applyToAmount(100));

        $amount = 1369; // 13.69 units
        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString, false);
        $this->assertEquals(86.31, $discount->applyToAmount(100));
    }

    /**
     * @throws RandomException
     */
    public function testZeroDiscountAmount(): void
    {
        $amount = 0;

        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString);
        $this->assertEquals(100, $discount->applyToAmount(100));

        $discount = Discount::createWithAmountAndExpirationDateNotPercentage($this->user, $amount, $this->expiresAtString);
        $this->assertEquals(100, $discount->applyToAmount(100));
    }

    /**
     * @throws RandomException
     */
    public function testFullDiscountAmount(): void
    {
        $amount = 10000;

        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString);
        $this->assertEquals(0, $discount->applyToAmount(100));
    }

    /**
     * @throws RandomException
     */
    public function testDiscountAmountGreaterThanOriginal(): void
    {
        $amount = 15000; // 150 units

        $this->ExpectException(InvalidArgumentException::class);
        $this->factory->create($this->user, $amount, $this->expiresAtString);
    }

    /**
     * @throws RandomException
     */
    public function testNegativeDiscountAmount(): void
    {
        $amount = -500;

        $this->expectException(InvalidArgumentException::class);
        $this->factory->create($this->user, $amount, $this->expiresAtString);
    }

    /**
     * @throws RandomException
     */
    public function testValidExpirationDate(): void
    {
        $amount = 10;

        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString);
        $this->assertInstanceOf(Discount::class, $discount);
    }

    /**
     * @throws RandomException
     */
    public function testInvalidExpirationDate(): void
    {
        $amount = 10;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The expiration date must be at least +1 day from today.');
        $expiresAt = new DateTimeImmutable();
        $invalidExpiresAt = $expiresAt->format('Y-m-d H:i:s');
        $this->factory->create($this->user, $amount, $invalidExpiresAt);
    }

    /**
     * @throws RandomException
     */
    public function testZeroOriginalAmount(): void
    {
        $this->assertEquals(0, $this->discount->applyToAmount(0));

        $amount = 1369;
        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString);
        $this->assertEquals(0, $discount->applyToAmount(0));
    }

    /**
     * @throws RandomException
     */
    public function testDiscountAmountEqualToOriginal(): void
    {
        $amount = 10000;

        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString);
        $this->assertEquals(0, $discount->applyToAmount(100));
    }

    /**
     * @throws RandomException
     */
    public function testZeroDiscountAmountAndZeroOriginalAmount(): void
    {
        $amount = 0;

        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString);
        $this->assertEquals(0, $discount->applyToAmount(0));

        // Test with a zero fixed amount discount
        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString);
        $this->assertEquals(0, $discount->applyToAmount(0));
    }

    /**
     * @throws RandomException
     * @throws ReflectionException
     */
    public function testExpiredDiscount(): void
    {
        $reflection = new ReflectionClass($this->discount);
        $property = $reflection->getProperty('expiresAt');
        $property->setValue($this->discount, new DateTimeImmutable('-1 day'));

        $this->assertFalse($this->discount->isValid());
    }

    /**
     * @throws RandomException
     */
    public function testInactiveDiscount(): void
    {
        $this->assertFalse($this->discount->isValid());
    }

    /**
     * @throws Exception
     */
    public function testItemSpecificDiscount(): void
    {
        $project = $this->createMock(Project::class);

        $this->discount->setTargetProject($project);
        $this->assertEquals($project, $this->discount->getTargetProject());
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function testDiscountNotApplicableToAnyItems(): void
    {
        $project = $this->createMock(Project::class);
        $amount = 2050;

        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString, true, $project);
        $this->assertEquals(100, $discount->applyToAmount(100));
    }

    /**
     * @throws RandomException
     */
    public function testDiscountApplicableToTotalAmount(): void
    {
        $amount = 2050;

        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString);
        $this->assertEquals(79.5, $this->discount->applyToAmount(100));
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function testProjectSpecificDiscount(): void
    {
        $amount = 2000;
        $project = $this->createMock(Project::class);

        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString, null, $project);

        $this->assertEquals(80, $discount->applyToAmount(100, $project));

        $otherProject = $this->createMock(Project::class);
        $this->assertEquals(100, $discount->applyToAmount(100, $otherProject));

        // Test applying with no project specified
        $this->assertEquals(100, $discount->applyToAmount(100));
    }

    /**
     * @throws RandomException
     */
    public function testDiscountNotPercentage(): void
    {
        $amount = 2050;

        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString, false);
        $this->assertEquals(79.5, $discount->applyToAmount(100));
    }

    /**
     * @throws ReflectionException
     */
    public function testCodeGenerator(): void
    {
        // Use reflection to access the private static method
        $reflectionClass = new ReflectionClass(Discount::class);
        $codeGeneratorMethod = $reflectionClass->getMethod('codeGenerator');

        $code = $codeGeneratorMethod->invoke(null);

        // Check it's a valid format (alphanumeric uppercase string)
        $this->assertMatchesRegularExpression('/^[A-Z0-9]+$/', $code);
    }

    /**
     * @throws RandomException
     */
    public function testExpirationDateValidation(): void
    {
        $amount = 2000;

        // Test with past date (should throw exception)
        $pastDate = new DateTimeImmutable('-2 days');
        $pastDateString = $pastDate->format('Y-m-d H:i:s');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The expiration date must be at least +1 day from today.');
        Discount::createWithAmountAndExpirationDate($this->user, $amount, $pastDateString);
    }

    /**
     * @throws Exception
     */
    public function testDiscountApproverCanApproveDiscount(): void
    {
        // Arrange
        $this->user->method('hasRole')
            ->willReturnCallback(function ($role) {
                $roleValue = $role instanceof UserRole ? $role->value : $role;
                return $roleValue === UserRole::DISCOUNT_APPROVER->value;
            });

        $canApprove = $this->discount->canBeApprovedBy($this->user);

        // Assert
        $this->assertTrue($canApprove);
    }

    public function testAdminCanApproveDiscount(): void
    {
        // Arrange
        $user = $this->createMock(User::class);
        $user->method('hasRole')
            ->willReturnCallback(function($role) {
                $roleValue = $role instanceof UserRole ? $role->value : $role;
                return $roleValue === UserRole::ADMIN->value;
            });

        // Act
        $canApprove = $this->discount->canBeApprovedBy($user);

        // Assert
        $this->assertTrue($canApprove);
    }

    public function testRegularUserCannotApproveDiscount(): void
    {
        // Arrange
        $user = $this->createMock(User::class);
        $user->method('hasRole')->willReturn(false);

        // Act
        $canApprove = $this->discount->canBeApprovedBy($user);

        // Assert
        $this->assertFalse($canApprove);
    }

    /**
     * @throws RandomException
     */
    public function testUpdateExpirationDate(): void
    {
        $amount = 2000;

        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString);

        $newExpiresAt = new DateTimeImmutable('+5 days');
        $discount->updateExpirationDate($newExpiresAt);

        $this->assertEquals($newExpiresAt, $discount->expiresAt());
    }

    public function testCompleteValidExpirationDate(): void
    {
        $amount = 1000;

        $discount = $this->factory->create($this->user, $amount, $this->expiresAtString);
        $this->assertEquals($this->expiresAtString, $discount->expiresAt()->format('Y-m-d H:i:s'));
    }
}
