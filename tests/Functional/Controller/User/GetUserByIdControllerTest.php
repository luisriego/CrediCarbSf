<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\Functional\Controller\ControllerTestBase;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetUserByIdControllerTest extends ControllerTestBase
{
    protected string $userId;

    public function setUp(): void
    {
        parent::setUp();
        $adminUser = static::getContainer()->get(UserRepositoryInterface::class)->findOneByEmail('admin@api.com');
        $this->userId = $adminUser->getId();
    }

    /**
     * @throws Exception
     */
    public function testGetUserById(): void
    {
        self::$admin->request(Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->userId),
            [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . self::ADMIN_TOKEN,]
        );

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('user', $responseData);
    }
}