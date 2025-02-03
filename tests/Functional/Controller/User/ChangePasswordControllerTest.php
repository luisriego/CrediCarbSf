<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\Functional\Controller\ControllerTestBase;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordControllerTest extends FunctionalTestBase
{
    protected string $userId;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testChangePasswordSuccessfully(): void
    {
        $payload = [
            'oldPassword' => 'password!',
            'newPassword' => 'new-password'
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::CHANGE_USER_PASSWORD_ENDPOINT, $this->adminId),
            [], [], [],
            \json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testChangePasswordFailBecauseWrongPassword(): void
    {
        $payload = [
            'oldPass' => 'a-wrong-password',
            'newPass' => 'new-password'
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::CHANGE_USER_PASSWORD_ENDPOINT, $this->userId),
            [], [], [],
            \json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testChangePasswordWithoutAuthMustFail(): void
    {
        self::$baseClient->setServerParameter('HTTP_Authorization', '');
        $payload = [
            'oldPass' => 'password',
            'newPass' => 'new-password'
        ];

        self::$baseClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::CHANGE_USER_PASSWORD_ENDPOINT, $this->userId),
            [], [], [],
            \json_encode($payload)
        );

        $response = self::$baseClient->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}