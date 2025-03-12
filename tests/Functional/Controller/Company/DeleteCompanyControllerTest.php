<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Domain\Model\Company;
use App\Domain\Model\User;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    }

/*     public function testDeleteCompanySuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    } */

    public function testDeleteNonExistingCompany(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, 'non-existing-company-id'),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testDeleteCompanyWithoutPermission(): void
    {
        // Simulate an unauthorized user
        self::$baseClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyId),
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testDeleteCompanyWithInvalidId(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, 'invalid-id'),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}