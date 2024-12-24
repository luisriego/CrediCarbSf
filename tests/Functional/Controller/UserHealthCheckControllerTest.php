<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserHealthCheckControllerTest extends ControllerTestBase
{
    private const ENDPOINT = '/user/health-check';

    public function testUserHealthCheck(): void
    {
        self::$admin->request(Request::METHOD_GET, self::ENDPOINT);

        $response = self::$admin->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertEquals('Module User up and running!', $responseData['message']);
    }
}