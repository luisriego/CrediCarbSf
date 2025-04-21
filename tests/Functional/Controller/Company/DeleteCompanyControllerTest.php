<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Domain\Model\Company;
use App\Domain\Model\User;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function sprintf;

class DeleteCompanyControllerTest extends FunctionalTestBase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testDeleteCompanyWithAssociatedUsersSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals('{"error":"Cannot delete company with associated users"}', $response->getContent());
    }
}
