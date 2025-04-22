<?php

namespace App\Tests\Unit\Application\UseCase\Company;

use App\Application\UseCase\Company\UpdateCompanyService\Dto\UpdateCompanyInputDto;
use App\Application\UseCase\Company\UpdateCompanyService\UpdateCompanyService;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Policy\CompanyPolicyInterface;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\Unit\Application\UseCase\Company\Mother\UpdateCompanyInputDtoMother;
use App\Tests\Unit\Domain\Model\Mother\CompanyMother;
use App\Tests\Unit\Domain\Model\Mother\UserMother;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


class UpdateCompanyTest extends TestCase
{
    private UpdateCompanyService $updateCompanyService;
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
        $this->updateCompanyService = new UpdateCompanyService(
            $this->companyRepository,
            $this->userRepository,
            $this->companyPolicy
        );
    }

    public function testUpdateCompanyWithNonExistingIdThrowsException(): void
    {
        // Arrange
        $nonExistingId = 'e7b6c15d-fa92-47bc-8289-9db382ae0378';
        $inputDto = UpdateCompanyInputDtoMother::withNonExistingId();

        $this->companyPolicy
            ->expects($this->once())
            ->method('canUpdateOrFail')
            ->with($nonExistingId)
            ->willThrowException(new ResourceNotFoundException('Company not found'));

        // Assert
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Company not found');

        // Act
        $this->updateCompanyService->handle($inputDto);
    }

    public function testUpdateCompanyThrowsExceptionWhenUserHasNoAccess(): void
    {
        // Arrange
        $inputDto = UpdateCompanyInputDtoMother::withValidData();

        $this->companyPolicy
            ->expects($this->once())
            ->method('canUpdateOrFail')
            ->with($inputDto->id)
            ->willThrowException(new AccessDeniedException('User has no access'));

        // Assert
        $this->expectException(AccessDeniedException::class);

        // Act
        $this->updateCompanyService->handle($inputDto);
    }

    public function testUpdateCompanyThrowsExceptionWhenFantasyNameIsTheSame(): void
    {
        // Arrange
        $company = CompanyMother::create(fantasyName: 'Updated Company Name');
        $inputDto = UpdateCompanyInputDtoMother::withValidData(
            fantasyName: 'Updated Company Name'
        );

        $this->companyPolicy
            ->expects($this->once())
            ->method('canUpdateOrFail')
            ->with($inputDto->id)
            ->willThrowException(new InvalidArgumentException('Fantasy name is the same'));

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fantasy name is the same');

        // Act
        $this->updateCompanyService->handle($inputDto);
    }

    public function testUpdateCompanyThrowsExceptionWhenUserNotFound(): void
    {
        // Arrange
        $inputDto = UpdateCompanyInputDtoMother::withValidData();

        $this->userRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with($inputDto->userId)
            ->willThrowException(new ResourceNotFoundException('User not found'));

        // Assert
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        // Act
        $this->updateCompanyService->handle($inputDto);
    }

    public function testUpdateCompanyThrowsExceptionWhenFantasyNameIsEmpty(): void
    {
        // Arrange
        $company = CompanyMother::create();
        $inputDto = UpdateCompanyInputDtoMother::withValidData(
            fantasyName: ''
        );

        $this->companyPolicy
            ->expects($this->once())
            ->method('canUpdateOrFail')
            ->with($inputDto->id)
            ->willThrowException(new InvalidArgumentException('Fantasy name must be between 5 and 100 characters'));

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fantasy name must be between 5 and 100 characters');

        // Act
        $this->updateCompanyService->handle($inputDto);
    }

    public function testUpdateCompanyThrowsExceptionWhenFantasyNameIsTooLong(): void
    {
        // Arrange
        $company = CompanyMother::create();
        $tooLongName = str_repeat('a', 256); // Assuming max length is 255
        $inputDto = UpdateCompanyInputDtoMother::withValidData(
            fantasyName: $tooLongName
        );

        $this->companyPolicy
            ->expects($this->once())
            ->method('canUpdateOrFail')
            ->with($inputDto->id)
            ->willThrowException(new InvalidArgumentException('Fantasy name must be between 5 and 100 characters'));

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fantasy name must be between 5 and 100 characters');

        // Act
        $this->updateCompanyService->handle($inputDto);
    }

    public function testUpdateCompanyWithNullFantasyName(): void
    {
        // Arrange
        $company = CompanyMother::create();
        $inputDto = UpdateCompanyInputDtoMother::withValidData(
            fantasyName: ''
        );

        $this->companyPolicy
            ->expects($this->once())
            ->method('canUpdateOrFail')
            ->with($inputDto->id)
            ->willThrowException(new InvalidArgumentException('Fantasy name must be between 5 and 100 characters'));

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fantasy name must be between 5 and 100 characters');

        // Act
        $this->updateCompanyService->handle($inputDto);
    }
}