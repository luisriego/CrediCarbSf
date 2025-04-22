<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\UseCase\Company;

use App\Application\UseCase\Company\DeleteCompanyService\DeleteCompanyService;
use App\Application\UseCase\Company\DeleteCompanyService\Dto\DeleteCompanyInputDto;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Exception\Company\CompanyHasUsersException;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Policy\CompanyPolicyInterface;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\Unit\Application\UseCase\Company\Mother\DeleteCompanyInputDtoMother;
use App\Tests\Unit\Domain\Model\Mother\CompanyMother;
use App\Tests\Unit\Domain\Model\Mother\UserMother;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeleteCompanyTest extends TestCase
{
    private DeleteCompanyService $deleteCompanyService;
    private CompanyRepositoryInterface|MockObject $companyRepository;
    private CompanyPolicyInterface|MockObject $companyPolicy;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->companyPolicy = $this->createMock(CompanyPolicyInterface::class);
        $this->deleteCompanyService = new DeleteCompanyService(
            $this->companyRepository,
            $this->companyPolicy
        );
    }

    public function testDeleteCompanySuccessfully(): void
    {
        // Arrange
        $inputDto = DeleteCompanyInputDtoMother::withValidData();
        $company = CompanyMother::create();

        $this->companyPolicy
            ->expects($this->once())
            ->method('canDeleteOrFail')
            ->with($inputDto->id);

        $this->companyRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with($inputDto->id)
            ->willReturn($company);

        $this->companyRepository
            ->expects($this->once())
            ->method('remove')
            ->with($company, true);

        // Act
        $this->deleteCompanyService->handle($inputDto);

        // No assertion needed as we expect no exception
        $this->assertTrue(true);
    }

    public function testDeleteCompanyThrowsExceptionWhenUserNotAuthorized(): void
    {
        // Arrange
        $inputDto = DeleteCompanyInputDtoMother::withValidData();

        $this->companyPolicy
            ->expects($this->once())
            ->method('canDeleteOrFail')
            ->with($inputDto->id)
            ->willThrowException(new AccessDeniedException('Unauthorized user'));

        // Assert
        $this->expectException(AccessDeniedException::class);

        // Act
        $this->deleteCompanyService->handle($inputDto);
    }

    public function testDeleteCompanyThrowsExceptionWhenCompanyHasUsers(): void
    {
        // Arrange
        $inputDto = DeleteCompanyInputDtoMother::withValidData();
        $company = CompanyMother::create();

        $this->companyPolicy
            ->expects($this->once())
            ->method('canDeleteOrFail')
            ->with($inputDto->id);

        $this->companyRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with($inputDto->id)
            ->willReturn($company);

        $this->companyRepository
            ->expects($this->once())
            ->method('remove')
            ->with($company, true)
            ->willThrowException(CompanyHasUsersException::deleteFromMessage());

        // Assert
        $this->expectException(CompanyHasUsersException::class);
        $this->expectExceptionMessage('');

        // Act
        $this->deleteCompanyService->handle($inputDto);
    }
}