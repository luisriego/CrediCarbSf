<?php 

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateCompanyTest extends FunctionalTestBase
{
    protected string $companyId;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUpdateCompanySuccessfully(): void
    {
        $payload = [
            'fantasyName' => 'Updated Company Name'
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
            [], [], [], json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}