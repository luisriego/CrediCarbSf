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
    private UserRepositoryInterface|MockObject $userRepository;
    private CompanyPolicyInterface|MockObject $companyPolicy;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->companyPolicy = $this->createMock(CompanyPolicyInterface::class);
        $this->deleteCompanyService = new DeleteCompanyService(
            $this->companyRepository,
            $this->userRepository,
            $this->companyPolicy
        );
    }

    public function testDeleteCompanySuccessfully(): void
    {
        // Arrange
        $inputDto = DeleteCompanyInputDtoMother::withValidData();
        $company = CompanyMother::create();
        $user = UserMother::withAdminRole();

        $this->userRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with($inputDto->userId)
            ->willReturn($user);

        $this->companyPolicy
            ->expects($this->once())
            ->method('canDelete')
            ->with($user, $inputDto->id)
            ->willReturn(true);

        $this->companyRepository
            ->expects($this->once())
            ->method('existById')
            ->with($inputDto->id)
            ->willReturn(true);

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

    public function testDeleteCompanyThrowsExceptionWhenCompanyNotFound(): void
    {
        // Arrange
        $inputDto = DeleteCompanyInputDtoMother::withNonExistingId();
        $user = UserMother::withAdminRole();

        $this->userRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with($inputDto->userId)
            ->willReturn($user);

        $this->companyPolicy
            ->expects($this->once())
            ->method('canDelete')
            ->with($user, $inputDto->id)
            ->willReturn(true);

        $this->companyRepository
            ->expects($this->once())
            ->method('existById')
            ->with($inputDto->id)
            ->willReturn(false);

        // Assert
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage("Company not found with id: {$inputDto->id}");

        // Act
        $this->deleteCompanyService->handle($inputDto);
    }

    public function testDeleteCompanyThrowsExceptionWhenUserNotAuthorized(): void
    {
        // Arrange
        $inputDto = DeleteCompanyInputDtoMother::withValidData();
        $user = UserMother::withUserRole();

        $this->userRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with($inputDto->userId)
            ->willReturn($user);

        $this->companyPolicy
            ->expects($this->once())
            ->method('canDelete')
            ->with($user, $inputDto->id)
            ->willReturn(false);

        // Assert
        $this->expectException(AccessDeniedException::class);

        // Act
        $this->deleteCompanyService->handle($inputDto);
    }

    public function testDeleteCompanyThrowsExceptionWhenUserNotFound(): void
    {
        // Arrange
        $inputDto = DeleteCompanyInputDtoMother::withValidData();

        $this->userRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with($inputDto->userId)
            ->willThrowException(new ResourceNotFoundException("User not found with id: {$inputDto->userId}"));

        // Assert
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage("User not found with id: {$inputDto->userId}");

        // Act
        $this->deleteCompanyService->handle($inputDto);
    }

    public function testDeleteCompanyThrowsExceptionWhenCompanyHasUsers(): void
    {
        // Arrange
        $inputDto = DeleteCompanyInputDtoMother::withValidData();
        $company = CompanyMother::create();
        $user = UserMother::withAdminRole();

        $this->userRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with($inputDto->userId)
            ->willReturn($user);

        $this->companyPolicy
            ->expects($this->once())
            ->method('canDelete')
            ->with($user, $inputDto->id)
            ->willReturn(true);

        $this->companyRepository
            ->expects($this->once())
            ->method('existById')
            ->with($inputDto->id)
            ->willReturn(true);

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