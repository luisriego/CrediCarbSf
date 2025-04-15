<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Application\UseCase\Company\CreateCompany\CreateCompany;
use App\Application\UseCase\Company\CreateCompany\Dto\CreateCompanyInputDto;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Tests\Functional\FunctionalTestBase;
use App\Tests\Unit\Domain\Model\Mother\CompanyMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateCompanyControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/company/create';

    /**
     * @throws \JsonException
     */
    public function testCreateCompanySuccessfully(): void
    {
        // Arrange
        $payload = [
            'id' => "cbf070cc-80d2-4bfe-9fa6-a96b97ffb0da",
            'taxpayer' => "14.121.957/0001-09",
            'fantasyName' => "company->fantasyName"
        ];

        // Act
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );

        // Assert
        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }
}