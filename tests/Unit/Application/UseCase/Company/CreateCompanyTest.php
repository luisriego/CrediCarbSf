<?php

namespace App\Tests\Unit\Application\UseCase\Company;

use App\Application\UseCase\Company\CreateCompany\CreateCompany;
use App\Domain\Bus\Event\EventBus;
use App\Domain\Exception\Company\CompanyAlreadyExistsException;
use App\Domain\Model\Company;
use App\Domain\Policy\CompanyPolicyInterface;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class CreateCompanyTest extends TestCase
{
    private CompanyRepositoryInterface|MockObject $companyRepository;
    private CompanyPolicyInterface|MockObject $companyPolicy;
    private EventBus|MockObject $eventBus;
    private CreateCompany $useCase;
    private CompanyId $validId;
    private CompanyTaxpayer $validTaxpayer;
    private CompanyName $validFantasyName;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->companyPolicy = $this->createMock(CompanyPolicyInterface::class);
        $this->eventBus = $this->createMock(EventBus::class);
        $this->useCase = new CreateCompany($this->companyRepository, $this->companyPolicy, $this->eventBus);

        // Create valid value objects for tests
        $this->validId = CompanyId::random();
        $this->validTaxpayer = CompanyTaxpayer::fromString('33592510002521');
        $this->validFantasyName = CompanyName::fromString('Test Company');
    }

    public function testCreateCompanySuccessfully(): void
    {
        // Arrange
        $this->companyPolicy
            ->expects($this->once())
            ->method('canCreateOrFail');

        $this->companyRepository
            ->expects($this->once())
            ->method('validateTaxpayerUniqueness')
            ->with($this->validTaxpayer);

        $this->companyRepository
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->callback(function (Company $company) {
                    return $company->id() === $this->validId->value()
                        && $company->taxpayer() === $this->validTaxpayer->value()
                        && $company->fantasyName() === $this->validFantasyName->value();
                }),
                true
            );

        // Act
        $this->useCase->create($this->validId, $this->validTaxpayer, $this->validFantasyName);
    }

    public function testCreateCompanyThrowsExceptionWhenPolicyFails(): void
    {
        // Arrange
        $policyException = new \DomainException('User is not authorized to create a company');

        $this->companyPolicy
            ->expects($this->once())
            ->method('canCreateOrFail')
            ->willThrowException($policyException);

        $this->companyRepository
            ->expects($this->never())
            ->method('validateTaxpayerUniqueness');

        $this->companyRepository
            ->expects($this->never())
            ->method('add');

        // Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('User is not authorized to create a company');

        // Act
        $this->useCase->create($this->validId, $this->validTaxpayer, $this->validFantasyName);
    }

    public function testCreateCompanyThrowsExceptionWhenTaxpayerAlreadyExists(): void
    {
        // Arrange
        $duplicateException = new CompanyAlreadyExistsException(Response::HTTP_CONFLICT, 'Company with taxpayer ' . $this->validTaxpayer->value() . ' already exists');

        $this->companyPolicy
            ->expects($this->once())
            ->method('canCreateOrFail');

        $this->companyRepository
            ->expects($this->once())
            ->method('validateTaxpayerUniqueness')
            ->with($this->validTaxpayer)
            ->willThrowException($duplicateException);

        $this->companyRepository
            ->expects($this->never())
            ->method('add');

        // Assert
        $this->expectException(CompanyAlreadyExistsException::class);
        $this->expectExceptionMessage('Company with taxpayer ' . $this->validTaxpayer->value() . ' already exists');

        // Act
        $this->useCase->create($this->validId, $this->validTaxpayer, $this->validFantasyName);
    }

    /**
     * Test that when creating a company with the factory method, all properties are set correctly
     */
    public function testCompanyIsCreatedWithCorrectProperties(): void
    {
        // Arrange
        $this->companyPolicy
            ->expects($this->once())
            ->method('canCreateOrFail');

        // Capture the Company object that is passed to the add method
        $capturedCompany = null;
        $this->companyRepository
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->callback(function (Company $company) use (&$capturedCompany) {
                    $capturedCompany = $company;
                    return true;
                }),
                true
            );

        // Act
        $this->useCase->create($this->validId, $this->validTaxpayer, $this->validFantasyName);

        // Assert
        $this->assertNotNull($capturedCompany);
        $this->assertEquals($this->validId->value(), $capturedCompany->id());
        $this->assertEquals($this->validTaxpayer->value(), $capturedCompany->taxpayer());
        $this->assertEquals($this->validFantasyName->value(), $capturedCompany->fantasyName());
    }
}
