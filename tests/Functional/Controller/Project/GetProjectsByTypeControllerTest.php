<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Tests\Functional\FunctionalTestBase;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function sprintf;

class GetProjectsByTypeControllerTest extends FunctionalTestBase
{
    protected string $validType = 'Reforestation';
    protected string $invalidType = 'INVALID_TYPE';

    public function setUp(): void
    {
        parent::setUp();
        // Initialize any required data for the tests
    }

    /**
     * @throws Exception
     */
    public function testGetProjectsByTypeSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('/api/v1/projectss/%s', $this->validType),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('projects', $responseData);
    }

    public function testGetProjectsByTypeWithInvalidType(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('/api/v1/projectss/%s', $this->invalidType),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$authenticatedClient->getResponse();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testGetProjectsByTypeUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_GET,
            sprintf('/api/v1/projectss/%s', $this->validType),
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
    public function testGetProjectsByTypeWithAnyLoggedInUser(): void
    {
        self::$anotherAuthenticatedClient->request(
            Request::METHOD_GET,
            sprintf('/api/v1/projectss/%s', $this->validType),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$anotherAuthenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('projects', $responseData);
    }
}
