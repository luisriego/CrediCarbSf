<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\UseCase\Company;

use App\Application\UseCase\Company\CreateCompany\CreateCompany;
use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyOutputDto;
use App\Domain\Exception\Company\CompanyAlreadyExistsException;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Tests\Unit\Application\UseCase\Company\Mother\CreateCompanyInputDtoMother;
use App\Tests\Unit\Domain\Model\Mother\CompanyMother;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


final class CreateCompanyTest extends TestCase
{
    private CreateCompany $createCompanyUseCase;
    private CompanyRepositoryInterface|MockObject $companyRepository;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->createCompanyUseCase = new CreateCompany($this->companyRepository);
    }

    public function testShouldCreateCompanySuccessfully(): void
    {
        // Arrange
        $company = CompanyMother::create();
        $inputDto = CreateCompanyInputDtoMother::fromCompany($company);

        $this->companyRepository
            ->expects($this->once())
            ->method('validateTaxpayerUniqueness')
            ->with($company->taxpayer());

        $this->companyRepository
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->callback(function ($savedCompany) use ($company) {
                    return $savedCompany->id() === $company->id() &&
                        $savedCompany->taxpayer() === $company->taxpayer() &&
                        $savedCompany->fantasyName() === $company->fantasyName();
                }),
                true
            );

        // Act
        $outputDto = $this->createCompanyUseCase->handle($inputDto);

        // Assert
        $this->assertInstanceOf(CreateCompanyOutputDto::class, $outputDto);
        $this->assertEquals($company->id(), $outputDto->companyId);
    }

    public function testShouldThrowExceptionWhenTaxpayerIsEmpty(): void
    {
        // Arrange
        $inputDto = CreateCompanyInputDtoMother::withEmptyTaxpayer();

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The Taxpayer identifier cannot be empty.');

        // Act
        $this->createCompanyUseCase->handle($inputDto);
    }

    public function testShouldThrowExceptionWhenFantasyNameIsEmpty(): void
    {
        // Arrange
        $inputDto = CreateCompanyInputDtoMother::withEmptyFantasyName();

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The Company Name cannot be empty.');

        // Act
        $this->createCompanyUseCase->handle($inputDto);
    }

    public function testShouldThrowExceptionWhenTaxpayerAlreadyExists(): void
    {
        // Arrange
        $company = CompanyMother::create();
        $inputDto = CreateCompanyInputDtoMother::fromCompany($company);

        $this->companyRepository
            ->expects($this->once())
            ->method('validateTaxpayerUniqueness')
            ->with($company->taxpayer())
            ->willThrowException(CompanyAlreadyExistsException::createFromTaxPayer($company->taxpayer()));

        // Assert
        $this->expectException(CompanyAlreadyExistsException::class);

        // Act
        $this->createCompanyUseCase->handle($inputDto);
    }

    public function testShouldHandleCustomCompanyData(): void
    {
        // Arrange
        $customTaxpayer = '33592510015500';
        $customFantasyName = 'Custom Company Name';
        $company = CompanyMother::withCustomData($customTaxpayer, $customFantasyName);
        $inputDto = CreateCompanyInputDtoMother::fromCompany($company);

        $this->companyRepository
            ->expects($this->once())
            ->method('validateTaxpayerUniqueness')
            ->with($customTaxpayer);

        $this->companyRepository
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->callback(function ($savedCompany) use ($customTaxpayer, $customFantasyName) {
                    return $savedCompany->taxpayer() === $customTaxpayer &&
                        $savedCompany->fantasyName() === $customFantasyName;
                }),
                true
            );

        // Act
        $outputDto = $this->createCompanyUseCase->handle($inputDto);

        // Assert
        $this->assertNotEmpty($outputDto->companyId);
    }

    public function testShouldThrowExceptionWhenTaxpayerHasInvalidFormat(): void
    {
        // Arrange
        $company = CompanyMother::create();
        $inputDto = CreateCompanyInputDtoMother::create(
            $company->id(),
            'invalid-format-123',
            $company->fantasyName()
        );

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Taxpayer length');

        // Act
        $this->createCompanyUseCase->handle($inputDto);
    }

}
