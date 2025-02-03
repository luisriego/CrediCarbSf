<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateCompanyTest extends FunctionalTestBase
{
    protected string $companyId;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUpdateCompanySuccessfully(): void
    {
        $payload = [
            'fantasyName' => 'Updated Company Name'
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
            [], [], [], json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testUpdateCompanyWithInvalidId(): void
    {
        $payload = [
            'fantasyName' => 'Updated Company Name'
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, 'invalid-id'),
            [], [], [], json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testUpdateCompanyUnauthorized(): void
    {
        $payload = [
            'fantasyName' => 'Updated Company Name'
        ];

        // Simulate an unauthorized user
        self::$baseClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
            [], [], [], json_encode($payload)
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testUpdateCompanyWithNullFantasyName(): void
    {
        $payload = [
            'fantasyName' => null
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
            [], [], [], json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testUpdateCompanyWithEmptyFantasyName(): void
    {
        $payload = [
            'fantasyName' => ''
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
            [], [], [], json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testUpdateCompanyWithSameFantasyName(): void
    {
        $payload = [
            'fantasyName' => 'Vale S.A. - Filial (Sao Gonçalo do Rio Abaixo)' // Assume this is the current name
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
            [], [], [], json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}