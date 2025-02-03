<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Tests\Functional\FunctionalTestBase;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetProjectsByStatusControllerTest extends FunctionalTestBase
{
    protected string $validStatus = 'PLANNED';
    protected string $invalidStatus = 'INVALID_STATUS';

    public function setUp(): void
    {
        parent::setUp();
        // Initialize any required data for the tests
    }

    /**
     * @throws Exception
     */
    public function testGetProjectsByStatusSuccessfully(): void
    {
        self::$authenticatedClient->request(Request::METHOD_GET,
            sprintf('/api/projects/%s', $this->validStatus),
            [], [], ['CONTENT_TYPE' => 'application/json']
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('projects', $responseData);
    }

    public function testGetProjectsByStatusWithInvalidStatus(): void
    {
        self::$authenticatedClient->request(Request::METHOD_GET,
            sprintf('/api/projects/%s', $this->invalidStatus),
            [], [], ['CONTENT_TYPE' => 'application/json']
        );

        $response = self::$authenticatedClient->getResponse();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testGetProjectsByStatusUnauthorized(): void
    {
        self::$baseClient->request(Request::METHOD_GET,
            sprintf('/api/projects/%s', $this->validStatus),
            [], [], ['CONTENT_TYPE' => 'application/json']
        );

        $response = self::$baseClient->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testGetProjectsByStatusWithAnyLoggedInUser(): void
    {
        self::$anotherAuthenticatedClient->request(Request::METHOD_GET,
            sprintf('/api/projects/%s', $this->validStatus),
            [], [], ['CONTENT_TYPE' => 'application/json']
        );

        $response = self::$anotherAuthenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('projects', $responseData);
    }
}