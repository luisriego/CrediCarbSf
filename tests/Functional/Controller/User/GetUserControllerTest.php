<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Tests\Functional\FunctionalTestBase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class GetUserControllerTest extends FunctionalTestBase
{
    /**
     * @throws Exception
     */
    public function testGetAllUsers(): void
    {
        self::$superAdminClient->request('GET', '/api/user/all', [], [], ['CONTENT_TYPE' => 'application/json']);

        $response = self::$superAdminClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('users', $responseData);
    }
}
