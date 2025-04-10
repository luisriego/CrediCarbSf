<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Application\UseCase\Company\CreateCompany\CreateCompany;
use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyInputDto;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Tests\Unit\Domain\Model\Mother\CompanyMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateCompanyTest extends TestCase
{
    private CreateCompany $createCompanyUseCase;
    private CompanyRepositoryInterface|MockObject $companyRepository;

    protected function setUp(): void
    {
        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->createCompanyUseCase = new CreateCompany($this->companyRepository);
    }

    public function testShouldCreateCompanySuccessfully(): void
    {
        // Arrange
        $company = CompanyMother::create();
        $inputDto = CreateCompanyInputDto::create(
            $company->id(),
            $company->taxpayer(),
            $company->fantasyName(),
        );

        $this->companyRepository
            ->expects($this->once())
            ->method('validateTaxpayerUniqueness')
            ->with($company->taxpayer());

        $this->companyRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(Company::class), true);

        // Act
        $outputDto = $this->createCompanyUseCase->handle($inputDto);

        // Assert
        $this->assertNotEmpty($outputDto->companyId);
    }

    public function testShouldThrowExceptionWhenTaxpayerIsEmpty(): void
    {
        // Arrange
        $company = CompanyMother::create();
        $inputDto = CreateCompanyInputDto::create(
            $company->id(),
            '', // empty taxpayer
            $company->fantasyName()
        );
    
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The following fields cannot be empty: taxpayer');
    
        // Act
        $this->createCompanyUseCase->handle($inputDto);
    }

    public function testShouldThrowExceptionWhenFantasyNameIsEmpty(): void
    {
        // Arrange
        $company = CompanyMother::create();
        $inputDto = CreateCompanyInputDto::create(
            $company->id(),
            $company->taxpayer(),
            '',
        );

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The following fields cannot be empty: fantasyName');

        // Act
        $this->createCompanyUseCase->handle($inputDto);
    }

    public function testShouldThrowExceptionWhenTaxpayerAlreadyExists(): void
    {
        // Arrange
        $company = CompanyMother::create();
        $inputDto = CreateCompanyInputDto::create(
            $company->id(),
            $company->taxpayer(),
            $company->fantasyName(),
        );

        $this->companyRepository
            ->expects($this->once())
            ->method('validateTaxpayerUniqueness')
            ->with($company->taxpayer())
            ->willThrowException(new InvalidArgumentException('Taxpayer already exists'));

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Taxpayer already exists');

        // Act
        $this->createCompanyUseCase->handle($inputDto);
    }
}