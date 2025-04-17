<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCompanyByNameControllerTest extends FunctionalTestBase
{
    protected string $companyName;
    protected const ENDPOINT_COMPANY = '/api/company/by-name';

    public function setUp(): void
    {
        parent::setUp();
        // Set a company name that exists in your test database
        $this->companyName = 'Vale S.A. - Matriz';
    }

    public function testGetCompanyByNameSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s?name=%s', self::ENDPOINT_COMPANY, urlencode($this->companyName)),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
}