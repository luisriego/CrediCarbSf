<?php

namespace App\Tests\Unit\Application\UseCase\Company;

use App\Application\UseCase\Company\GetCompanyByNameService\GetCompanyByNameService;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Service\CompanyFinder;
use App\Tests\Unit\Domain\Model\Mother\CompanyMother;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetCompanyByNameServiceTest extends TestCase
{
    private CompanyRepositoryInterface|MockObject $companyRepository;
    private GetCompanyByNameService $service;
    private const NON_EXISTING_NAME = 'Non-existing Company';
    private const EXISTING_NAME = 'Test Company';
    private const SHORT_NAME = 'Test'; // Assuming there's a minimum length requirement
    private const LONG_NAME = 'This is a very long company name that exceeds the maximum length allowed for company names in the system which might cause validation errors'; // Assuming there's a maximum length
    private const INVALID_CHARS_NAME = 'Test@Company#123';

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->service = new GetCompanyByNameService($this->companyRepository);
        $this->companyFinder = new CompanyFinder($this->companyRepository);

    }

    public function testHandleReturnsCompaniesWhenFound(): void
    {
        // Arrange
        $name = self::EXISTING_NAME;
        $company1 = CompanyMother::create(fantasyName: $name);
        $company2 = CompanyMother::create(
            id: 'd94070cc-80d2-4bfe-9fa6-a96b97ffb0db',
            fantasyName: $name
        );
        $expectedCompanies = [$company1, $company2];

        $this->companyRepository->expects($this->once())
            ->method('findByFantasyNameOrFail')
            ->with($name)
            ->willReturn($expectedCompanies);

        // Act
        $result = $this->service->handle($name);

        // Assert
        $this->assertSame($expectedCompanies, $result);
        $this->assertCount(2, $result);
    }

    public function testHandleReturnsEmptyArrayWhenNoCompaniesFound(): void
    {
        // Arrange
        $name = self::NON_EXISTING_NAME;

        $this->companyRepository->expects($this->once())
            ->method('findByFantasyNameOrFail')
            ->with($name)
            ->willThrowException(new ResourceNotFoundException('No companies found with that name'));

        // Assert
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('No companies found with that name');

        // Act
        $this->service->handle($name);
    }

    public function testHandleWithShortNameThrowsException(): void
    {
        // Arrange
        $name = self::SHORT_NAME;

        $this->companyRepository->expects($this->never())
            ->method('findByFantasyNameOrFail');

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fantasy name must be between 5 and 100 characters');

        // Act
        $this->service->handle($name);
    }

    public function testHandleWithLongNameThrowsException(): void
    {
        // Arrange
        $name = self::LONG_NAME;

        $this->companyRepository->expects($this->never())
            ->method('findByFantasyNameOrFail');

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fantasy name must be between 5 and 100 characters');

        // Act
        $this->service->handle($name);
    }

    public function testHandleWithInvalidCharsThrowsException(): void
    {
        // Arrange
        $name = self::INVALID_CHARS_NAME;

        $this->companyRepository->expects($this->once())
            ->method('findByFantasyNameOrFail')
            ->with($name)
            ->willThrowException(new InvalidArgumentException(
                'Company name contains disallowed special characters'));

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Company name contains disallowed special characters');

        // Act
        $this->service->handle($name);
    }

    public function testHandleWithEmptyNameThrowsException(): void
    {
        // Arrange
        $name = '';

        $this->companyRepository->expects($this->never())
            ->method('findByFantasyNameOrFail');

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The Company Name cannot be empty.');

        // Act
        $this->service->handle($name);
    }
}