<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\Functional\Controller\ControllerTestBase;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivateUserControllerTest extends FunctionalTestBase
{
    protected string $userId;
    protected string $token;

    public function setUp(): void
    {
        parent::setUp();
        $adminUser = static::getContainer()->get(UserRepositoryInterface::class)->findOneByEmail('admin@api.com');
        $this->userId = $adminUser->getId();
        $this->token = $adminUser->getToken();
    }

    public function testActivateUserSuccessfully(): void
    {
        $payload = [
            'id' => $this->userId,
            'token' => $this->token,
        ];

        self::$baseClient->request(
            Request::METHOD_PUT,
            self::ACTIVATE_USER_ENDPOINT,
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testActivateUserWithInvalidToken(): void
    {
        $payload = [
            'id' => $this->userId,
            'token' => 'invalid_token',
        ];

        self::$baseClient->request(
            Request::METHOD_PUT,
            self::ACTIVATE_USER_ENDPOINT,
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testActivateUserWithNonExistingUser(): void
    {
        $payload = [
            'id' => self::NON_EXISTING_USER_ID,
            'token' => $this->token,
        ];

        self::$baseClient->request(
            Request::METHOD_PUT,
            self::ACTIVATE_USER_ENDPOINT,
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testActivateUserWithMissingToken(): void
    {
        $payload = [
            'id' => $this->userId,
        ];

        self::$baseClient->request(
            Request::METHOD_PUT,
            self::ACTIVATE_USER_ENDPOINT,
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}