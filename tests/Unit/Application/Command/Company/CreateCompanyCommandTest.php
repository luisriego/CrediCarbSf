<?php

namespace App\Tests\Unit\Application\Command\Company;

use App\Application\Command\Company\CreateCompanyCommand;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\Taxpayer;
use App\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;


class CreateCompanyCommandTest extends TestCase
{
    public function testCreateWithValidData(): void
    {
        $id = Uuid::random();
        $taxpayer = Taxpayer::fromString('33592510002521');
        $fantasyName = CompanyName::fromString('Valid Company Name');

        $command = CreateCompanyCommand::create(
            $id,
            $taxpayer,
            $fantasyName
        );

        $this->assertInstanceOf(CreateCompanyCommand::class, $command);
    }

    public function testIsImmutable(): void
    {
        $command = CreateCompanyCommand::create(
            Uuid::random(),
            Taxpayer::fromString('33592510002521'),
            CompanyName::fromString('Valid Company Name')
        );

        $reflectionClass = new \ReflectionClass($command);
        $readOnlyProps = array_filter(
            $reflectionClass->getProperties(),
            fn(\ReflectionProperty $prop) => $prop->isReadOnly()
        );

        $this->assertCount(3, $readOnlyProps);
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
        \App\Domain\ValueObject\Taxpayer::fromString($taxpayer);
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
