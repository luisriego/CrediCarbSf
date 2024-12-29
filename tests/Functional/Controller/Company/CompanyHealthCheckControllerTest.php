<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Tests\Functional\Controller\ControllerTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyHealthCheckControllerTest extends ControllerTestBase
{
    private const ENDPOINT = '/company/health-check';

    public function testCompanyHealthCheck(): void
    {
        self::$admin->request(Request::METHOD_GET, self::ENDPOINT);

        $response = self::$admin->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertEquals('Module Company up and running!', $responseData['message']);
    }
}