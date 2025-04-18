<?php

namespace App\Tests\Unit\Application\UseCase\Company;

use App\Application\UseCase\Company\GetCompanyByTaxpayerService\GetCompanyByTaxpayerService;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\CompanyTaxpayer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetCompanyByTaxpayerTest extends TestCase
{
    private CompanyRepositoryInterface|MockObject $companyRepository;
    private GetCompanyByTaxpayerService $service;
    private const NON_EXISTING_TAXPAYER = '04.036.170/0001-87';
    private const INVALID_TAXPAYER = '04.036.170/0001-78';
    private const MALFORMED_TAXPAYER = '0403617000018';
    private const EMPTY_TAXPAYER = '';

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->service = new GetCompanyByTaxpayerService($this->companyRepository);
    }

    public function testGetCompanyByNonExistingTaxpayer(): void
    {
        // Arrange
        $taxpayer = self::NON_EXISTING_TAXPAYER;

        $this->companyRepository->expects($this->once())
            ->method('findOneByTaxpayerOrFail')
            ->with(CompanyTaxpayer::fromString($taxpayer)->value())
            ->willThrowException(new ResourceNotFoundException('Company not found'));

        // Assert
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Company not found');

        // Act
        $this->service->handle($taxpayer);
    }

    public function testGetCompanyByInvalidTaxpayer(): void
    {
        // Arrange
        $taxpayer = self::INVALID_TAXPAYER;

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid CNPJ digits');

        // Act
        $this->service->handle($taxpayer);
    }

    public function testGetCompanyByEmptyTaxpayer(): void
    {
        // Arrange
        $taxpayer = self::EMPTY_TAXPAYER;

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The Taxpayer identifier cannot be empty.');

        // Act
        $this->service->handle($taxpayer);
    }

    public function testGetCompanyByMalformedTaxpayer(): void
    {
        // Arrange
        $taxpayer = self::MALFORMED_TAXPAYER;

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Taxpayer length');

        // Act
        $this->service->handle($taxpayer);
    }
}