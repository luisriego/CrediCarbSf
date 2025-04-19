<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;

class UserHealthCheckControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/v1/users/health-check';

    public function testUserHealthCheck(): void
    {
        self::$authenticatedClient->request(Request::METHOD_GET, self::ENDPOINT);

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertEquals('Module User up and running!', $responseData['message']);
    }
}
