<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Application\UseCase\Company\CreateCompany\CreateCompany;
use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyInputDto;
use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyOutputDto;
use App\Domain\Exception\Company\CompanyAlreadyExistsException;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Tests\Functional\FunctionalTestBase;
use PHPUnit\Framework\MockObject\MockObject;
use TypeError;

class CreateCompanyTest extends FunctionalTestBase
{
    private CreateCompany $createCompany;
    private readonly CompanyRepositoryInterface|MockObject $companyRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->createCompany = new CreateCompany($this->companyRepository);
    }

    public function testCreateCompanySuccessfully(): void
    {
        $inputDto = new CreateCompanyInputDto(
            'Test Company',
            '33.592.510/0025-21',
        );

        $this->companyRepository
            ->expects($this->once())
            ->method('existByTaxpayer')
            ->with('33592510002521')
            ->willReturn(null);

        $this->companyRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function (Company $company): bool {
                return $company->fantasyName() === 'Test Company';
            }));

        $responseDTO = $this->createCompany->handle($inputDto);

        self::assertInstanceOf(CreateCompanyOutputDto::class, $responseDTO);
    }

    public function testCreateCompanyAlreadyExists(): void
    {
        $this->expectException(CompanyAlreadyExistsException::class);

        $inputDto = new CreateCompanyInputDto(
            'Test Company',
            '33.592.510/0025-21',
        );

        $this->companyRepository
            ->expects($this->once())
            ->method('existByTaxpayer')
            ->with('33592510002521')
            ->willReturn(Company::create('33592510002521', 'Test Company'));

        $this->createCompany->handle($inputDto);
    }

    public function testCreateCompanyWithEmptyTaxpayer(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inputDto = new CreateCompanyInputDto(
            'Test Company',
            '',
        );

        $this->createCompany->handle($inputDto);
    }

    public function testCreateCompanyWithEmptyFantasyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The following fields cannot be empty: fantasyName');

        $inputDto = new CreateCompanyInputDto(
            '',
            '33.592.510/0025-21',
        );

        $this->createCompany->handle($inputDto);
    }

    public function testCreateCompanyWithInvalidTaxpayerLength(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inputDto = new CreateCompanyInputDto(
            'Test Company',
            '123',
        );

        $this->createCompany->handle($inputDto);
    }

    public function testCreateCompanyWithNullInputDto(): void
    {
        $this->expectException(TypeError::class);

        $this->createCompany->handle(null);
    }

    public function testCreateCompanyFailedBecauseSequentialCnpj(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid CNPJ digits');

        $inputDto = new CreateCompanyInputDto(
            'Test Company',
            '12345678901234',
        );

        $responseDTO = $this->createCompany->handle($inputDto);

        self::assertInstanceOf(CreateCompanyOutputDto::class, $responseDTO);
    }
}
