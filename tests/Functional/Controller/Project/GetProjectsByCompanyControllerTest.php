<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Tests\Functional\FunctionalTestBase;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function sprintf;

class GetProjectsByCompanyControllerTest extends FunctionalTestBase
{
    protected string $companyId;
    protected string $invalidCompanyId = 'INVALID_UUID';

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws Exception
     */
    public function testGetProjectsByCompanySuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('/api/v1/projectss/%s', $this->companyId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('projects', $responseData);
    }

    public function testGetProjectsByCompanyWithInvalidCompanyId(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('/api/v1/projectss/%s', $this->invalidCompanyId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$authenticatedClient->getResponse();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testGetProjectsByCompanyUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_GET,
            sprintf('/api/v1/projectss/%s', $this->companyId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$baseClient->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testGetProjectsByCompanyWithAnyLoggedInUser(): void
    {
        self::$anotherAuthenticatedClient->request(
            Request::METHOD_GET,
            sprintf('/api/v1/projectss/%s', $this->companyId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$anotherAuthenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('projects', $responseData);
    }

    /**
     * @throws Exception
     */
    public function testGetProjectsByCompanyWithNoProjects(): void
    {
        $emptyCompanyId = '123e4567-e89b-12d3-a456-426614174001'; // Assume this company has no projects

        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('/api/v1/projectss/%s', $emptyCompanyId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('projects', $responseData);
        $this->assertEmpty($responseData['projects']);
    }
}
