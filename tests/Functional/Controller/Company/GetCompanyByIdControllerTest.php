<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function sprintf;

class GetCompanyByIdControllerTest extends FunctionalTestBase
{
    protected string $companyId;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetCompanyByIdSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
}
