<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_encode;
use function sprintf;

class ChangePasswordControllerTest extends FunctionalTestBase
{
    protected string $userId;

    /**
     * @throws \JsonException
     */
    public function testChangePasswordSuccessfully(): void
    {
        $payload = [
            'oldPassword' => 'password!',
            'newPassword' => 'Password1',
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::CHANGE_USER_PASSWORD_ENDPOINT, $this->adminId),
            [],
            [],
            [],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );

        $response = self::$authenticatedClient->getResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testChangePasswordFailBecauseWrongPassword(): void
    {
        $payload = [
            'oldPass' => 'a-wrong-password',
            'newPass' => 'Password1',
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::CHANGE_USER_PASSWORD_ENDPOINT, $this->userId),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testChangePasswordWithoutAuthMustFail(): void
    {
        self::$baseClient->setServerParameter('HTTP_Authorization', '');
        $payload = [
            'oldPass' => 'password',
            'newPass' => 'Password1',
        ];

        self::$baseClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::CHANGE_USER_PASSWORD_ENDPOINT, $this->userId),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$baseClient->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
