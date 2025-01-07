<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Tests\Functional\Controller\ControllerTestBase;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyHealthCheckControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/company/health-check';

    public function testCompanyHealthCheck(): void
    {
        self::$baseClient->request(Request::METHOD_GET, self::ENDPOINT);

        $response = self::$baseClient->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertEquals('Module Company up and running!', $responseData['message']);
    }
}