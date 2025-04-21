<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateCompanyControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/v1/companies/create';

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