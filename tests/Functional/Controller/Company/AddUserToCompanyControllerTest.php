<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\Functional\Controller\ControllerTestBase;
use App\Tests\Functional\FunctionalTestBase;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddUserToCompanyControllerTest extends FunctionalTestBase
{
    protected string $companyId;
    protected string $userId;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAddUserToCompanySuccessfully(): void
    {
        $payload = [
            'userId' => $this->adminId,
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            sprintf(
                '%s/%s',
                self::ADD_USER_TO_COMPANY_ENDPOINT, $this->companyId),
            [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testAddUserToCompanyUnauthorized(): void
    {
        $payload = [
            'userId' => $this->userId,
        ];

        self::$baseClient->request(
            Request::METHOD_POST,
            sprintf(
                '%s/%s',
                self::ADD_USER_TO_COMPANY_ENDPOINT, $this->companyId),
            [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testAddUserToCompanyWithInvalidCompanyId(): void
    {
        $payload = [
            'userId' => $this->userId,
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            sprintf('%s/%s', self::ADD_USER_TO_COMPANY_ENDPOINT, 'invalid_company_id'),
            [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testAddUserToCompanyWithInvalidUserId(): void
    {
        $payload = [
            'userId' => 'invalid_user_id',
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            sprintf('%s/%s', self::ADD_USER_TO_COMPANY_ENDPOINT, $this->companyId),
            [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}