<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\UseCase\Company;

use App\Application\UseCase\Company\AddUserToCompanyService\AddUserToCompanyService;
use App\Application\UseCase\Company\AddUserToCompanyService\Dto\AddUserToCompanyInputDto;
use App\Application\UseCase\Company\AddUserToCompanyService\Dto\AddUserToCompanyOutputDto;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Policy\CompanyPolicyInterface;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\Unit\Domain\Model\Mother\CompanyMother;
use App\Tests\Unit\Domain\Model\Mother\UserMother;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AddUserToCompanyTest extends TestCase
{
    private AddUserToCompanyService $addUserToCompanyService;
    private CompanyRepositoryInterface|MockObject $companyRepository;
    private UserRepositoryInterface|MockObject $userRepository;
    private CompanyPolicyInterface|MockObject $companyPolicy;
    private const COMPANY_ID = 'e7b6c15d-fa92-47bc-8289-9db382ae0378';
    private const USER_ID = 'f8b7c16e-fb93-47cd-9290-0eb483ae0479';
    private const NON_EXISTING_COMPANY_ID = '3f46050a-bd15-419a-a37d-0867ec8c504b';
    private const NON_EXISTING_USER_ID = '4f56060b-ce16-429b-b48d-1977fd9c615c';
    private const INVALID_ID = '3f46050a-bd15-419a-a37d-0867ec8c5@#$';

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->companyPolicy = $this->createMock(CompanyPolicyInterface::class);
        $this->addUserToCompanyService = new AddUserToCompanyService(
            $this->companyRepository,
            $this->userRepository,
            $this->companyPolicy
        );
    }

    public function testAddUserToCompanySuccessfully(): void
    {
        // Arrange
        $inputDto = AddUserToCompanyInputDto::create(self::COMPANY_ID, self::USER_ID);
        $company = CompanyMother::create();
        $user = UserMother::create();

        $this->companyRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with(self::COMPANY_ID)
            ->willReturn($company);

        $this->userRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with(self::USER_ID)
            ->willReturn($user);

        $this->companyPolicy
            ->expects($this->once())
            ->method('canAddUserOrFail')
            ->with($company->id());

        $this->companyRepository
            ->expects($this->once())
            ->method('save')
            ->with($company, true);

        // Act
        $outputDto = $this->addUserToCompanyService->handle($inputDto);

        // Assert
        $this->assertInstanceOf(AddUserToCompanyOutputDto::class, $outputDto);
        $this->assertEquals(self::USER_ID, $outputDto->userId);
    }

    public function testAddUserToCompanyThrowsExceptionWhenCompanyNotFound(): void
    {
        // Arrange
        $inputDto = AddUserToCompanyInputDto::create(self::NON_EXISTING_COMPANY_ID, self::USER_ID);

        $this->companyRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with(self::NON_EXISTING_COMPANY_ID)
            ->willThrowException(new ResourceNotFoundException('Company not found'));

        // Assert
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Company not found');

        // Act
        $this->addUserToCompanyService->handle($inputDto);
    }

    public function testAddUserToCompanyThrowsExceptionWhenUserNotFound(): void
    {
        // Arrange
        $inputDto = AddUserToCompanyInputDto::create(self::COMPANY_ID, self::NON_EXISTING_USER_ID);
        $company = CompanyMother::create();

        $this->companyRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with(self::COMPANY_ID)
            ->willReturn($company);

        $this->userRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with(self::NON_EXISTING_USER_ID)
            ->willThrowException(new ResourceNotFoundException('User not found'));

        // Assert
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        // Act
        $this->addUserToCompanyService->handle($inputDto);
    }

    public function testAddUserToCompanyThrowsExceptionWhenUserHasNoAccess(): void
    {
        // Arrange
        $inputDto = AddUserToCompanyInputDto::create(self::COMPANY_ID, self::USER_ID);
        $company = CompanyMother::create();

        $this->companyRepository
            ->expects($this->once())
            ->method('findOneByIdOrFail')
            ->with(self::COMPANY_ID)
            ->willReturn($company);

        $this->companyPolicy
            ->expects($this->once())
            ->method('canAddUserOrFail')
            ->with($company->id())
            ->willThrowException(new AccessDeniedException('User has no access'));

        // Assert
        $this->expectException(AccessDeniedException::class);

        // Act
        $this->addUserToCompanyService->handle($inputDto);
    }

    public function testAddUserToCompanyThrowsExceptionWhenCompanyIdIsInvalid(): void
    {
        // Arrange
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid UID format'
        );

        // Act
        AddUserToCompanyInputDto::create(self::INVALID_ID, self::USER_ID);
    }

    public function testAddUserToCompanyThrowsExceptionWhenUserIdIsInvalid(): void
    {
        // Arrange
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid UID format'
        );

        // Act
        AddUserToCompanyInputDto::create(self::COMPANY_ID, self::INVALID_ID);
    }
}