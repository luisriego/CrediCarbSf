<?php

namespace App\Tests\Unit\Application\Command\Company;

use App\Application\Command\Company\CreateCompanyCommand;
use App\Application\Command\Company\CreateCompanyCommandHandler;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObjects\Uuid;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CreateCompanyCommandHandlerTest extends TestCase
{
    private CompanyRepositoryInterface $companyRepository;
    private CreateCompanyCommandHandler $handler;
    private string $validCompanyId;
    private string $validTaxpayer;
    private string $validFantasyName;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->handler = new CreateCompanyCommandHandler($this->companyRepository);
        $this->validCompanyId = Uuid::random()->value();
        $this->validTaxpayer = '33592510002521';
        $this->validFantasyName = 'Test Company';
    }


    /**
     * @throws Exception
     */
    public function testHandleCreatesAndSavesCompany(): void
    {
        // Arrange
        $companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $handler = new CreateCompanyCommandHandler($companyRepository);

        $companyId = Uuid::random()->value();
        $command = new CreateCompanyCommand(
            $companyId,
            'Test Company',
            '33592510002521'
        );

        // Expect the company to be saved
        $companyRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Company $company) use ($companyId) {
                return $company->id() === $companyId
                    && $company->taxpayer() === '33592510002521'
                    && $company->fantasyName() === 'Test Company';
            }));

        // Act
        $handler->__invoke($command);
    }

    /**
     * Corner case: Company with null fantasy name
     */
    public function testHandleCreatesCompanyWithNullFantasyName(): void
    {
        // Arrange
        $command = new CreateCompanyCommand(
            $this->validCompanyId,
            null,
            $this->validTaxpayer
        );

        $this->companyRepository->expects($this->once())
            ->method('validateTaxpayerUniqueness');

        $this->companyRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Company $company) {
                    return $company->id() === $this->validCompanyId
                        && $company->fantasyName() === null
                        && $company->taxpayer() === $this->validTaxpayer;
                }),
                true
            );

        // Act
        $this->handler->__invoke($command);
    }

    /**
     * Corner case: Duplicate taxpayer
     */
    public function testHandleThrowsExceptionForDuplicateTaxpayer(): void
    {
        // Arrange
        $command = new CreateCompanyCommand(
            $this->validCompanyId,
            $this->validFantasyName,
            $this->validTaxpayer
        );

        $this->companyRepository->expects($this->once())
            ->method('validateTaxpayerUniqueness')
            ->willThrowException(new \DomainException('Taxpayer already exists'));

        $this->companyRepository->expects($this->never())
            ->method('save');

        // Assert & Act
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Taxpayer already exists');

        $this->handler->__invoke($command);
    }

    /**
     * Corner case: Invalid taxpayer format
     */
    public function testHandleThrowsExceptionForInvalidTaxpayer(): void
    {
        // Arrange
        $invalidTaxpayer = '123456'; // Too short

        $command = new CreateCompanyCommand(
            $this->validCompanyId,
            $this->validFantasyName,
            $invalidTaxpayer
        );

        // We expect the Taxpayer::fromString to throw an exception
        // before even reaching the repository validation
        $this->companyRepository->expects($this->never())
            ->method('validateTaxpayerUniqueness');

        $this->companyRepository->expects($this->never())
            ->method('save');

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);

        $this->handler->__invoke($command);
    }

    /**
     * Corner case: Invalid fantasy name (too short)
     */
    public function testHandleThrowsExceptionForTooShortFantasyName(): void
    {
        // Arrange
        $tooShortFantasyName = 'ABC'; // Below minimum length

        $command = new CreateCompanyCommand(
            $this->validCompanyId,
            $tooShortFantasyName,
            $this->validTaxpayer
        );

        // ValidationTaxpayerUniqueness will be called before fantasy name validation
        $this->companyRepository->expects($this->once())
            ->method('validateTaxpayerUniqueness');

        $this->companyRepository->expects($this->never())
            ->method('save');

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);

        $this->handler->__invoke($command);
    }

    /**
     * Corner case: Invalid fantasy name (too long)
     */
    public function testHandleThrowsExceptionForTooLongFantasyName(): void
    {
        // Arrange
        $tooLongFantasyName = str_repeat('A', 101); // Exceeds maximum length

        $command = new CreateCompanyCommand(
            $this->validCompanyId,
            $tooLongFantasyName,
            $this->validTaxpayer
        );

        // ValidationTaxpayerUniqueness will be called before fantasy name validation
        $this->companyRepository->expects($this->once())
            ->method('validateTaxpayerUniqueness');

        $this->companyRepository->expects($this->never())
            ->method('save');

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);

        $this->handler->__invoke($command);
    }
}
