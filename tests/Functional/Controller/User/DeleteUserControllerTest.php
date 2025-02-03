<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteUserControllerTest extends FunctionalTestBase
{
    protected string $userId;

    public function setUp(): void
    {
        parent::setUp();
//        $adminUser = static::getContainer()->get(UserRepositoryInterface::class)->findOneByEmail('admin@api.com');
    }

    public function testDeleteUserSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->userId),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteNonExistingUser(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, self::NON_EXISTING_USER_ID),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @throws \Exception
     */
    public function testDeleteUserWithoutPermission(): void
    {
        self::$baseClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->adminId),
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testDeleteUserWithInvalidId(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, 'invalid-id'),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}