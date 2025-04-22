<?php

namespace App\Tests\Unit\Application\Command\Company;

use App\Application\Command\Company\CreateCompanyCommand;
use App\Application\Command\Company\CreateCompanyCommandHandler;
use App\Application\UseCase\Company\CreateCompany\CreateCompany;
use App\Domain\Bus\Event\EventBus;
use App\Domain\Event\CreateCompanyDomainEvent;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Model\Company;
use App\Domain\Policy\CompanyPolicyInterface;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyTaxpayer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CreateCompanyCommandHandlerTest extends TestCase
{
    private CompanyRepositoryInterface $companyRepository;
    private CompanyPolicyInterface $companyPolicy;
    private EventBus $eventBus;

    private CreateCompany $useCase;
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
        $this->companyPolicy = $this->createMock(CompanyPolicyInterface::class);
        $this->eventBus = $this->createMock(EventBus::class);

        $this->useCase = new CreateCompany($this->companyRepository, $this->companyPolicy, $this->eventBus);

        $this->handler = new CreateCompanyCommandHandler($this->useCase);

        $this->validCompanyId = CompanyId::random()->value();
        $this->validTaxpayer = '33592510002521';
        $this->validFantasyName = 'Test Company';
    }

    public function testHandleCreatesSavesCompanyAndPublishesEventSuccessfully(): void
    {
        $command = new CreateCompanyCommand(
            $this->validCompanyId,
            $this->validTaxpayer,
            $this->validFantasyName
        );

        $this->companyPolicy->expects($this->once())
        ->method('canCreateOrFail');

        $this->companyRepository->expects($this->once())
            ->method('validateTaxpayerUniqueness')
            ->with($this->callback(function (CompanyTaxpayer $taxpayer) {
                return $taxpayer->value() === $this->validTaxpayer;
            }));

        $this->companyRepository->expects($this->once())
        ->method('add')
            ->with(
                $this->callback(function (Company $company) {
                    return $company->id() === $this->validCompanyId
                        && $company->taxpayer() === $this->validTaxpayer
                        && $company->fantasyName() === $this->validFantasyName
                        && $company->isActive();
                }),
                true
            );

        $this->eventBus->expects($this->once())
        ->method('publish')
            ->with($this->callback(function (CreateCompanyDomainEvent $event) {
                return $event->aggregateId() === $this->validCompanyId
                    && $event->taxpayer() === $this->validTaxpayer
                    && $event->fantasyName() === $this->validFantasyName;
            }));

        // Act
        $this->handler->__invoke($command);
    }

    public function testHandleThrowsExceptionWhenPolicyDeniesCreation(): void
    {
        // Arrange
        $command = new CreateCompanyCommand(
            $this->validCompanyId,
            $this->validTaxpayer,
            $this->validFantasyName
        );

        $this->companyPolicy->expects($this->once())
            ->method('canCreateOrFail')
            ->willThrowException(AccessDeniedException::UnauthorizedUser());

        $this->companyRepository->expects($this->never())->method('validateTaxpayerUniqueness');
        $this->companyRepository->expects($this->never())->method('add');
        $this->eventBus->expects($this->never())->method('publish');

        // Assert
        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('The User has not the necessary permissions');

        // Act
        $this->handler->__invoke($command);
    }

    public function testHandleThrowsExceptionForDuplicateTaxpayer(): void
    {
        // Arrange
        $command = new CreateCompanyCommand(
            $this->validCompanyId,
            $this->validTaxpayer,
            $this->validFantasyName
        );

        // Policy configuration
        $this->companyPolicy->expects($this->once())
            ->method('canCreateOrFail');

        $this->companyRepository->expects($this->once())
            ->method('validateTaxpayerUniqueness')
            ->with($this->callback(fn(CompanyTaxpayer $t) => $t->value() === $this->validTaxpayer))
            ->willThrowException(new AccessDeniedException('Taxpayer already exists'));

        $this->companyRepository->expects($this->never())->method('add');
        $this->eventBus->expects($this->never())->method('publish');

        // Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Taxpayer already exists');

        // Act
        $this->handler->__invoke($command);
    }

    public function testHandleCreatesCompanyWithMinimumLengthFantasyName(): void
    {
        // Arrange
        $command = CreateCompanyCommand::create(
            $this->validCompanyId,
            $this->validTaxpayer,
            'Valid',
        );

        $this->companyRepository->expects($this->once())
            ->method('validateTaxpayerUniqueness');

        $this->companyRepository->expects($this->once())
            ->method('add')
            ->with(
                $this->callback(function (Company $company) {
                    return $company->fantasyName() === 'Valid';
                })
            );

        // Act
        $this->handler->__invoke($command);
    }
}