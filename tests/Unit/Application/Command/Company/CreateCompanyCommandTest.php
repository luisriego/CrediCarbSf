<?php

namespace App\Tests\Unit\Application\Command\Company;

use App\Application\Command\Company\CreateCompanyCommand;
use App\Domain\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;


class CreateCompanyCommandTest extends TestCase
{
    public function testCreateWithValidData(): void
    {
        $command = new CreateCompanyCommand(
            'Fantasy Company Name',
            '33.592.510/0025-21'
        );

        $this->assertEquals('Fantasy Company Name', $command->fantasyName());
        $this->assertEquals('33.592.510/0025-21', $command->taxpayer());
    }

    public function testIsImmutable(): void
    {
        $command = new CreateCompanyCommand(
            'Fantasy Company Name',
            '33.592.510/0025-21'
        );

        // Test that command properties cannot be altered
        $reflectionClass = new \ReflectionClass($command);
        $this->assertTrue($reflectionClass->isReadOnly());
    }

    /**
     * @dataProvider invalidTaxpayerDataProvider
     */
    public function testRejectsInvalidTaxpayerFormats(string $taxpayer): void
    {
        // Instead of testing the command directly, test the command handler
        // or test the Taxpayer value object directly
        $this->expectException(\App\Domain\Exception\InvalidArgumentException::class);

        // For example, if validation happens in Taxpayer::fromString
        \App\Domain\ValueObjects\Taxpayer::fromString($taxpayer);
    }

    private static function invalidTaxpayerDataProvider(): array
    {
        return [
            'too short' => ['123456'],
            'too long' => ['123456789012345678901'],
            'with letters' => ['12.345.ABC/0001-23'],
            'with special chars' => ['12.345.678/0001-@#'],
        ];
    }
}
