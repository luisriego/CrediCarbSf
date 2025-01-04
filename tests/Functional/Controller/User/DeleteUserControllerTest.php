<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\Functional\Controller\ControllerTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteUserControllerTest extends ControllerTestBase
{
    protected string $userId;

    public function setUp(): void
    {
        parent::setUp();
//        $adminUser = static::getContainer()->get(UserRepositoryInterface::class)->findOneByEmail('admin@api.com');
    }

    public function testDeleteUserSuccessfully(): void
    {
        self::$admin->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->userId),
        );

        $response = self::$admin->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteNonExistingUser(): void
    {
        self::$admin->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, self::NON_EXISTING_USER_ID),
        );

        $response = self::$admin->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @throws \Exception
     */
    public function testDeleteUserWithoutPermission(): void
    {
        $unauthorizedToken = self::NOT_VALID_TOKEN;

        self::$admin->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $unauthorizedToken));

        self::$admin->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->userId),
        );

        $response = self::$admin->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testDeleteUserWithInvalidId(): void
    {
        self::$admin->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, 'invalid-id'),
        );

        $response = self::$admin->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}