<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Tests\Functional\FunctionalTestBase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetAllProjectsControllerTest extends FunctionalTestBase
{
    use RefreshDatabaseTrait;

    private const ENDPOINT = '/api/project/all';

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws \Exception
     */
    public function testShouldGetAllProjectsSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            self::ENDPOINT,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('projects', $responseData);
    }

    public function testShouldReturnUnauthorizedWhenUserIsNotAuthenticated(): void
    {
        self::$baseClient->request(
            Request::METHOD_GET,
            self::ENDPOINT,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = self::$baseClient->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
