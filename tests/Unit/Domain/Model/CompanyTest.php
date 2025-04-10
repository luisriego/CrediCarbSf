<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Company;
use App\Domain\ValueObjects\FantasyName;
use App\Domain\ValueObjects\Taxpayer;
use App\Tests\Unit\Domain\Model\Mother\CompanyMother;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function str_repeat;

class CompanyTest extends TestCase
{
    public function testCreateValidCompany(): void
    {
        $company = CompanyMother::create();

        $this->assertEquals('33592510002521', $company->taxpayer());
        $this->assertEquals('Test Company', $company->fantasyName());
        $this->assertTrue($company->isActive());
        $this->assertNotNull($company->getId());
        $this->assertNotNull($company->getCreatedOn());
    }

    public function testCreateCompanyWithInvalidTaxpayerThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Taxpayer length');

        CompanyMother::withInvalidTaxpayer('invalidtax');
    }

    public function testActivateCompany(): void
    {
        $company = CompanyMother::inactive();
        $company->activate();

        $this->assertTrue($company->isActive());
    }

    public function testDeactivateCompany(): void
    {
        $company = CompanyMother::create();
        $company->deactivate();

        $this->assertFalse($company->isActive());
    }

    public function testDeactivateAlreadyInactiveCompanyThrowsException(): void
    {
        $company = CompanyMother::inactive();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('already inactive');

        $company->deactivate();
    }

    public function testActivateAlreadyActiveCompanyThrowsException(): void
    {
        $company = CompanyMother::create();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('already active');

        $company->activate();
    }

    public function testUpdateFantasyName(): void
    {
        $company = CompanyMother::create();
        $company->updateFantasyName('New Name');

        $this->assertEquals('New Name', $company->fantasyName());
    }

    public function testCreateCompanyWithTooShortFantasyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fantasy name must be between 5 and 100 characters');

        CompanyMother::withInvalidFantasyName('AB');
    }

    public function testCreateCompanyWithTooLongFantasyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fantasy name must be between 5 and 100 characters');

        CompanyMother::withInvalidFantasyName(str_repeat('X', 200));
    }

    public function testCreateCompanyWithMinimumValidFantasyName(): void
    {
        $company = CompanyMother::create(
            fantasyName: FantasyName::fromString('Valid')
        );
        
        $this->assertEquals('Valid', $company->fantasyName());
        $this->assertTrue($company->isActive());
    }

    public function testCreateCompanyWithNonNumericTaxpayer(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Taxpayer length');

        CompanyMother::withInvalidTaxpayer('12345678ABCD12');
    }

    public function testCreateCompanyWithValidBrazilianTaxpayerCode(): void
    {
        $validCnpj = '33592510002521';
        $company = CompanyMother::create(
            taxpayer: Taxpayer::fromString($validCnpj)
        );

        $this->assertEquals($validCnpj, $company->taxpayer());
        $this->assertTrue($company->isActive());
    }

    public function testCreateCompanyWithInvalidBrazilianTaxpayerCode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid CNPJ digits');

        CompanyMother::withInvalidTaxpayer('12345678901234');
    }
}