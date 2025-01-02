<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Tests\Functional\Controller\ControllerTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectHealthCheckControllerTest extends ControllerTestBase
{
    private const ENDPOINT = '/api/project/health-check';

    public function testProjectHealthCheck(): void
    {
        self::$admin->request(Request::METHOD_GET, self::ENDPOINT);

        $response = self::$admin->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertEquals('Module Project up and running!', $responseData['message']);
    }
}