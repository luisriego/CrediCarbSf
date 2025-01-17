<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Tests\Functional\FunctionalTestBase;
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
            'GET',
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
            'GET',
            self::ENDPOINT,
            [], [], ['CONTENT_TYPE' => 'application/json']
        );

        $response = self::$baseClient->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}