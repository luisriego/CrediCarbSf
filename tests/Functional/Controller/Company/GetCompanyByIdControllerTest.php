<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function sprintf;

class GetCompanyByIdControllerTest extends FunctionalTestBase
{
    protected string $companyId;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetCompanyByIdSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testGetCompanyByNonExistingId(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, '3f46050a-bd15-419a-a37d-0867ec8c504b'),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetCompanyByIdWithoutPermission(): void
    {
        // Simulate an unauthorized user
        self::$baseClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testGetCompanyByInvalidId(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, '3f46050a-bd15-419a-a37d-0867ec8c5@#$'),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testGetCompanyByEmptyId(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, ''),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
