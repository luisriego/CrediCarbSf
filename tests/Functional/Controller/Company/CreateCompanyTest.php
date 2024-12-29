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
use App\Tests\Functional\Controller\ControllerTestBase;
use PHPUnit\Framework\MockObject\MockObject;

class CreateCompanyTest extends ControllerTestBase
{
    private const ENDPOINT = '/company/create';

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
            '12345678901234',
        );

        $this->companyRepository
            ->expects($this->once())
            ->method('existByTaxpayer')
            ->with('12345678901234')
            ->willReturn(null);

        $this->companyRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function (Company $company): bool {
                return $company->getFantasyName() === 'Test Company';
            }));

        $responseDTO = $this->createCompany->handle($inputDto);

        self::assertInstanceOf(CreateCompanyOutputDto::class, $responseDTO);
    }

    public function testCreateCompanyAlreadyExists(): void
    {
        $this->expectException(CompanyAlreadyExistsException::class);

        $inputDto = new CreateCompanyInputDto(
            'Test Company',
            '12345678901234'
        );

        $this->companyRepository
            ->expects($this->once())
            ->method('existByTaxpayer')
            ->with('12345678901234')
            ->willReturn(Company::create('12345678901234', 'Test Company'));

        $this->createCompany->handle($inputDto);
    }

    public function testCreateCompanyWithEmptyTaxpayer(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inputDto = new CreateCompanyInputDto(
            'Test Company',
            ''
        );

        $this->createCompany->handle($inputDto);
    }

    public function testCreateCompanyWithEmptyFantasyName(): void
{
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('The following fields cannot be empty: fantasyName');

    $inputDto = new CreateCompanyInputDto(
        '',
        '12345678901234'
    );

    $this->createCompany->handle($inputDto);
}

    public function testCreateCompanyWithInvalidTaxpayerLength(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inputDto = new CreateCompanyInputDto(
            'Test Company',
            '123'
        );

        $this->createCompany->handle($inputDto);
    }

    public function testCreateCompanyWithNullInputDto(): void
    {
        $this->expectException(\TypeError::class);

        $this->createCompany->handle(null);
    }
}