<?php

namespace App\Tests\Unit\Application\Command\Company;

use App\Application\Command\Company\CreateCompanyCommand;
use App\Application\Command\Company\CreateCompanyCommandHandler;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;
use App\Domain\ValueObject\Uuid;
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
        $this->validCompanyId = CompanyId::random()->value();
        $this->validTaxpayer = '33592510002521';
        $this->validFantasyName = 'Test Company';
    }

    /**
     * @throws Exception
     */
    public function testHandleCreatesAndSavesCompany(): void
    {
        // Arrange
        $command = CreateCompanyCommand::create(
            $this->validCompanyId,
            $this->validTaxpayer,
            $this->validFantasyName
        );

        $this->companyRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Company $company) {
                return $company->id() === $this->validCompanyId
                    && $company->taxpayer() === $this->validTaxpayer
                    && $company->fantasyName() === $this->validFantasyName;
            }));

        // Act
        $this->handler->__invoke($command);
    }

    public function testHandleThrowsExceptionForDuplicateTaxpayer(): void
    {
        // Arrange
        $command = CreateCompanyCommand::create(
            $this->validCompanyId,
            $this->validTaxpayer,
            $this->validFantasyName
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

    public function testHandleThrowsExceptionForInvalidTaxpayer(): void
    {
        // Arrange
        $invalidTaxpayer = '123456'; // Too short

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Taxpayer length');

        // Act - Exception will be thrown when creating the Command
        CreateCompanyCommand::create(
            $this->validCompanyId,
            $invalidTaxpayer,
            $this->validFantasyName
        );

        // We never get here
        $this->companyRepository->expects($this->never())
            ->method('validateTaxpayerUniqueness');
        $this->companyRepository->expects($this->never())
            ->method('save');
    }

    public function testHandleCreatesCompanyWithMinimumLengthFantasyName(): void
    {
        // Arrange
        $command = CreateCompanyCommand::create(
            $this->validCompanyId,
            $this->validTaxpayer,
            'Valid',  // 5 caracteres, longitud mínima válida
        );

        $this->companyRepository->expects($this->once())
            ->method('validateTaxpayerUniqueness');

        $this->companyRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Company $company) {
                    return $company->fantasyName() === 'Valid';
                })
            );

        // Act
        $this->handler->__invoke($command);
    }
}