<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Tests\Functional\FunctionalTestBase;
use Doctrine\DBAL\Exception;
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

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testShouldReturnEmptyListWhenNoProjectsExist(): void
    {
        // Clear the projects table
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $connection = $entityManager->getConnection();
        $connection->executeStatement('DELETE FROM project');

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
}