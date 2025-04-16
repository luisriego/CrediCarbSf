<?php

namespace App\Tests\Unit\Application\UseCase\Company;

use App\Application\UseCase\Company\GetCompanyByIdService\GetCompanyByIdService;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Repository\CompanyRepositoryInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetCompanyByIdTest extends TestCase
{
    private CompanyRepositoryInterface|MockObject $companyRepository;
    private GetCompanyByIdService $service;
    private const NON_EXISTING_ID = '3f46050a-bd15-419a-a37d-0867ec8c504b';
    private const INVALID_ID = '3f46050a-bd15-419a-a37d-0867ec8c5@#$';
    private const EMPTY_ID = '';

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->service = new GetCompanyByIdService($this->companyRepository);
    }

    public function testGetCompanyByNonExistingId(): void
    {
        // Arrange
        $id = self::NON_EXISTING_ID;

        $this->companyRepository->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with($id)
            ->willThrowException(new ResourceNotFoundException('Company not found'));

        // Assert
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Company not found');

        // Act
        $this->service->handle($id);
    }

    public function testGetCompanyByInvalidId(): void
    {
        // Arrange
        $id = self::INVALID_ID; // Use the invalid format ID here

        // The repository should not be called with invalid ID
        $this->companyRepository->expects($this->never())
            ->method('findOneByIdOrFail');

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            '<App\Domain\ValueObject\CompanyId> does not allow the value <3f46050a-bd15-419a-a37d-0867ec8c5@#$>.');

        // Act
        $this->service->handle($id);
    }

    public function testGetCompanyByEmptyId(): void
    {
        // Arrange
        $id = self::EMPTY_ID;

        // The repository should not be called with empty ID
        $this->companyRepository->expects($this->never())
            ->method('findOneByIdOrFail');

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('<App\Domain\ValueObject\CompanyId> does not allow the value <>.');

        // Act
        $this->service->handle($id);
    }
}
