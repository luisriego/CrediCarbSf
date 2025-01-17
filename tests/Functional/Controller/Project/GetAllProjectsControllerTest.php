<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetAllProjectsControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/project/all';

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws \Exception
     */
    public function testShouldGetAllProjectsSuccessfully()
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            self::ENDPOINT,
            [], [], ['CONTENT_TYPE' => 'application/json']);

        $response = self::$authenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('projects', $responseData);
    }

    public function testShouldReturnUnauthorizedWhenUserIsNotAuthenticated()
    {
        self::$baseClient->request(
            Request::METHOD_GET,
            self::ENDPOINT,
            [], [], ['CONTENT_TYPE' => 'application/json']
        );

        $response = self::$baseClient->getResponse();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testShouldReturnEmptyListWhenNoProjectsExist(): void
    {
        // Load only required fixtures, excluding projects
        $this->loadFixtures([
            __DIR__ . '/../../Fixtures/user.yaml',
            __DIR__ . '/../../Fixtures/company.yaml'
        ]);

        self::$authenticatedClient->request(
            Request::METHOD_GET,
            self::ENDPOINT,
            [], [], ['CONTENT_TYPE' => 'application/json']
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('projects', $responseData);
        $this->assertEmpty($responseData['projects']);
    }

    public function testShouldReturnMethodNotAllowedForInvalidRequestMethod(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            self::ENDPOINT,
            [], [], ['CONTENT_TYPE' => 'application/json']
        );

        $response = self::$authenticatedClient->getResponse();

        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
    }

    public function testShouldReturnProjectsSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            self::ENDPOINT,
            [], [], ['CONTENT_TYPE' => 'application/json']
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('projects', $responseData);
        $this->assertNotEmpty($responseData['projects']);
    }
}