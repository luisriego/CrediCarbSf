<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;

class ProjectHealthCheckControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/v1/projects/health-check';

    public function testProjectHealthCheck(): void
    {
        self::$baseClient->request(Request::METHOD_GET, self::ENDPOINT);

        $response = self::$baseClient->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertEquals('Module Project up and running!', $responseData['message']);
    }
}
