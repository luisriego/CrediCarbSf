<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Tests\Functional\Controller\ControllerTestBase;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterNewUserTest extends ControllerTestBase
{
    /**
     * @throws Exception
     */
    public function testCreateUser(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'age' => 25,
        ];

        self::$admin->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], \json_encode($payload));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertArrayHasKey('userId', $responseData);
        $this->assertNotEmpty($responseData['userId']);
    }

    /**
     * @throws Exception
     */
    public function testCreateUserWithMissingFields(): void
    {
        $payload = [
            'name' => 'Test User',
            // Missing email and password
            'age' => 25,
        ];

        self::$admin->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], \json_encode($payload));

        $response = self::$admin->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testCreateUserWithInvalidEmail(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'age' => 25,
        ];

        self::$admin->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], \json_encode($payload));

        $response = self::$admin->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testCreateUserWithShortPassword(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'age' => 25,
        ];

        self::$admin->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], \json_encode($payload));

        $response = self::$admin->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testCreateUserWithDuplicateEmail(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'age' => 25,
        ];

        // Create the first user
        self::$admin->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], \json_encode($payload));
        $response = self::$admin->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        // Try to create another user with the same email
        self::$admin->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], \json_encode($payload));
        $response = self::$admin->getResponse();
        $responseData = \json_decode($response->getContent(), true);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals('User with email <duplicate@example.com> already exists', $responseData['message']);
    }
}
