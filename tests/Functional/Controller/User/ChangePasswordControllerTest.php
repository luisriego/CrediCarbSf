<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\Functional\Controller\ControllerTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordControllerTest extends ControllerTestBase
{
    protected string $userId;

    public function setUp(): void
    {
        parent::setUp();
//        $adminUser = static::getContainer()->get(UserRepositoryInterface::class)->findOneByEmail('admin@api.com');
//        $this->userId = $adminUser->getId();
    }

    public function testChangePasswordSuccessfully(): void
    {
        $payload = [
            $this->adminId,
            'oldPassword' => 'Password1!',
            'newPassword' => 'new-password'
        ];

        self::$admin->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::CHANGE_USER_PASSWORD_ENDPOINT, $this->adminId),
            [], [], [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testChangePasswordFailBecauseWrongPassword(): void
    {
        $payload = [
            'oldPass' => 'a-wrong-password',
            'newPass' => 'new-password'
        ];

        self::$admin->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::CHANGE_USER_PASSWORD_ENDPOINT, $this->adminId),
            [], [], [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testChangePasswordWithoutAuthMustFail(): void
    {
        self::$admin->setServerParameter('HTTP_Authorization', '');
        $payload = [
            'oldPass' => 'password',
            'newPass' => 'new-password'
        ];

        self::$admin->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::CHANGE_USER_PASSWORD_ENDPOINT, $this->userId),
            [], [], [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}