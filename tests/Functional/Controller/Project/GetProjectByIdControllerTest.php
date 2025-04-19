<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Tests\Functional\FunctionalTestBase;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function sprintf;

class GetProjectByIdControllerTest extends FunctionalTestBase
{
    protected string $nonExistentProjectId = '00000000-0000-0000-0000-000000000000';
    protected string $invalidProjectId = 'invalid-uuid';

    public function setUp(): void
    {
        parent::setUp();
        // Initialize $projectId with a valid project ID from your test database
    }

    /**
     * @throws Exception
     */
    public function testGetProjectByIdSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', '/api/v1/projects', $this->projectId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('project', $responseData);
    }

    /**
     * @throws Exception
     */
    public function testGetProjectByIdWithInvalidId(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', '/api/v1/projects', $this->invalidProjectId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$authenticatedClient->getResponse();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testGetProjectByIdWithNonExistentId(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', '/api/v1/projects', $this->nonExistentProjectId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$authenticatedClient->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testGetProjectByIdUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', '/api/v1/projects', $this->projectId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$baseClient->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    //    /**
    //     * @throws Exception
    //     */
    //    public function testGetProjectByIdInactiveProject(): void
    //    {
    //        // Assuming you have a method to set a project as inactive in your test setup
    //        $this->setProjectAsInactive($this->projectId);
    //
    //        self::$authenticatedClient->request(Request::METHOD_GET,
    //            sprintf('%s/%s', '/api/v1/projects', $this->validProjectId),
    //            [], [], ['CONTENT_TYPE' => 'application/json']
    //        );
    //
    //        $response = self::$authenticatedClient->getResponse();
    //        $responseData = $this->getResponseData($response);
    //
    //        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    //        $this->assertArrayHasKey('project', $responseData);
    //        $this->assertFalse($responseData['project']['isActive']);
    //    }
}
