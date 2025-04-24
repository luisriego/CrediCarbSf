<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Company;
use App\Domain\Model\Project;
use App\Domain\Model\User;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\Taxpayer;
use App\Tests\Unit\Domain\Model\Mother\CompanyMother;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Exception;
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
            fantasyName: 'Valid'
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
            taxpayer: $validCnpj
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

    /**
     * @throws \ReflectionException
     * @throws Exception
     */
    public function testRegisterOwnedProject(): void
    {
        $company = CompanyMother::create();
        $project = $this->createMock(Project::class);

        $company->registerOwnedProject($project);

        $reflection = new \ReflectionClass($company);
        $property = $reflection->getProperty('ownedProjects');
        $ownedProjects = $property->getValue($company);

        $this->assertTrue($ownedProjects->contains($project));
    }

    public function testRegisterOwnedProjectWithInactiveCompanyThrowsException(): void
    {
        $company = CompanyMother::inactive();
        $project = $this->createMock(Project::class);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot register project for inactive company');

        $company->registerOwnedProject($project);
    }

    public function testRemoveUserFromCompany(): void
    {
        $company = CompanyMother::create();
        $user = $this->createMock(User::class);

        // First add the user to the company
        $reflection = new \ReflectionClass($company);
        $property = $reflection->getProperty('users');
        $users = $property->getValue($company);
        $users->add($user);

        // Now remove the user
        $company->removeUserFromCompany($user);

        // Assert the user has been removed
        $this->assertFalse($users->contains($user));
    }

    public function testRemoveUserFromCompanyWhenUserNotInCompanyDoesNothing(): void
    {
        $company = CompanyMother::create();
        $user = $this->createMock(User::class);

        // Get initial user count
        $reflection = new \ReflectionClass($company);
        $property = $reflection->getProperty('users');
        $users = $property->getValue($company);
        $initialCount = $users->count();

        // Try to remove a user that's not in the company
        $company->removeUserFromCompany($user);

        // Assert nothing has changed
        $this->assertEquals($initialCount, $users->count());
    }

    public function testPurchaseProject(): void
    {
        $company = CompanyMother::create();
        $project = $this->createMock(Project::class);

        $company->purchaseProject($project);

        // Assert the project is in the bought projects collection
        $reflection = new \ReflectionClass($company);
        $property = $reflection->getProperty('boughtProjects');
        $boughtProjects = $property->getValue($company);

        $this->assertTrue($boughtProjects->contains($project));
    }

    public function testPurchaseProjectWithInactiveCompanyThrowsException(): void
    {
        $company = CompanyMother::inactive();
        $project = $this->createMock(Project::class);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot purchase project with inactive company');

        $company->purchaseProject($project);
    }
}