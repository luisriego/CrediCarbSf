<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\Functional\FunctionalTestBase;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetUserByIdControllerTest extends FunctionalTestBase
{
    protected string $userId;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws Exception
     */
    public function testGetUserById(): void
    {
        self::$authenticatedClient->request(Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->userId),
            [], [], ['CONTENT_TYPE' => 'application/json']
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('user', $responseData);
    }
}