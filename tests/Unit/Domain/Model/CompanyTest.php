<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Company;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function str_repeat;

class CompanyTest extends TestCase
{
    public function testCreateValidCompany(): void
    {
        $company = Company::create('33592510002521', 'Test Company');

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

        Company::create('invalidtax', 'Some Company');
    }

    public function testActivateCompany(): void
    {
        $company = Company::create('33592510002521', 'Test Company');
        $company->deactivate();
        $company->activate();

        $this->assertTrue($company->isActive());
    }

    public function testDeactivateCompany(): void
    {
        $company = Company::create('33592510002521', 'Test Company');
        $company->deactivate();

        $this->assertFalse($company->isActive());
    }

    public function testDeactivateAlreadyInactiveCompanyThrowsException(): void
    {
        $company = Company::create('33592510002521', 'Test Company');
        $company->deactivate();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('already inactive');

        $company->deactivate();
    }

    public function testActivateAlreadyActiveCompanyThrowsException(): void
    {
        $company = Company::create('33592510002521', 'Test Company');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('already active');

        $company->activate();
    }

    public function testUpdateFantasyName(): void
    {
        $company = Company::create('33592510002521', 'Old Name');
        $company->updateFantasyName('New Name');

        $this->assertEquals('New Name', $company->fantasyName());
    }

    /**
     * Test creating a company with a fantasy name that's too short.
     * This assumes there's a length check that requires at least X characters.
     */
    public function testCreateCompanyWithTooShortFantasyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fantasy name must be between 5 and 100 characters');

        // "AB" is presumably shorter than the required minimum
        Company::create('33592510002521', 'AB');
    }

    /**
     * Test creating a company with a fantasy name that's too long.
     * This assumes there's a length check that enforces an upper limit.
     */
    public function testCreateCompanyWithTooLongFantasyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fantasy name must be between 5 and 100 characters');

        // A 200-character string exceeding any typical upper limit, e.g., 100
        $longName = str_repeat('X', 200);

        Company::create('33592510002521', $longName);
    }

    public function testCreateCompanyWithNullFantasyName(): void
    {
        $company = Company::create('33592510002521', null);
        $this->assertNull($company->fantasyName());
        $this->assertTrue($company->isActive());
    }

    /**
     * Test creating a company with a taxpayer of 14 digits but containing letters.
     * Should fail if only numeric is allowed.
     */
    public function testCreateCompanyWithNonNumericTaxpayer(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Taxpayer length');

        // This string has 14 characters, but includes letters
        Company::create('12345678ABCD12', 'Invalid Taxpayer');
    }

    /**
     * Test creating a company with a valid Brazilian CNPJ.
     * Adjust the CNPJ to match your real-world validation requirements.
     */
    public function testCreateCompanyWithValidBrazilianTaxpayerCode(): void
    {
        // Example of a 14-digit CNPJ that should pass your mod-11 validation
        // (replace with a real passing CNPJ for your logic)
        $validCnpj = '33592510002521';

        // This should not throw an exception if your code validates CNPJ properly
        $company = Company::create($validCnpj, 'Test Company with Valid CNPJ');

        $this->assertEquals($validCnpj, $company->taxpayer());
        $this->assertTrue($company->isActive());
    }

    /**
     * Test creating a company with an invalid Brazilian CNPJ.
     * Adjust the string to fail your mod-11 validation.
     */
    public function testCreateCompanyWithInvalidBrazilianTaxpayerCode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid CNPJ digits');
        // or whatever exception message you use

        // This CNPJ either has the wrong check digits or fails your domain’s validation
        $invalidCnpj = '12345678901234';

        // Since it's invalid, we expect an exception
        Company::create($invalidCnpj, 'Company with Invalid CNPJ');
    }
}
