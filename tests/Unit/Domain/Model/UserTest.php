<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\User;
use App\Domain\Security\PasswordHasherInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

    private PasswordHasherInterface $hasherMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->hasherMock = $this->createMock(PasswordHasherInterface::class);
        $this->hasherMock->method('hashPasswordForUser')
            ->willReturn('validPass123');
    }

    public function testSetPasswordHappyPath(): void
    {
        $user = new User('John Doe', 'john@example.com', '1nitialPass');
        $user->setPassword('validPass123', $this->hasherMock);

        $this->assertSame('validPass123', $user->getPassword());
    }

    public function testUpdatedOnIsUpdated(): void
    {
        $user = User::create('Test User', 'test@example.com', 'InitialPass');
        $oldUpdatedOn = $user->getUpdatedOn();

        // Trigger an update, for example:
        $user->setName('Updated User');
        $user->markAsUpdated();

        $newUpdatedOn = $user->getUpdatedOn();
        $this->assertNotNull($newUpdatedOn);
        $this->assertGreaterThan($oldUpdatedOn, $newUpdatedOn);
    }

    public function testNameTooShort(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new User('A', 'valid@example.com', 'ValidPass7');
    }

    public function testNameTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new User(str_repeat('X', 500), 'valid@example.com', 'ValidPass8');
    }

    public function testNegativeAge(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $user = new User('ValidName', 'valid@example.com', 'Pass8ok');
        $user->setAge(-1);
    }

    public function testPasswordTooShort(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $user = new User('ValidName', 'valid@example.com', 'Shor1');
        $user->setPassword('Shor1', $this->hasherMock);
    }

    public function testPasswordWithoutNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $user = new User('ValidName', 'valid@example.com', 'InvalidWithoutNumber');
        $user->setPassword('Shor1', $this->hasherMock);
    }
}
