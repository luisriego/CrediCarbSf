<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Tests\Functional\Controller\ControllerTestBase;
use Symfony\Component\HttpFoundation\Response;

class GetUserControllerTest extends ControllerTestBase
{
    /**
     * @throws \Exception
     */
    public function testGetAllUsers()
    {
        self::$admin->request('GET', '/api/user/all', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . self::ADMIN_TOKEN,
        ]);

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertArrayHasKey('users', $responseData);
    }
}