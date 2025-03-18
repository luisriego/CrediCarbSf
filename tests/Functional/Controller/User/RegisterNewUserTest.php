<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Tests\Functional\FunctionalTestBase;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_encode;

class RegisterNewUserTest extends FunctionalTestBase
{
    /**
     * @throws Exception
     */
    public function testCreateUser(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'age' => 25,
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::CREATE_USER_ENDPOINT,
            [], [], [],
            json_encode($payload, JSON_THROW_ON_ERROR));

        $response = self::$authenticatedClient->getResponse();
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

        self::$authenticatedClient->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$authenticatedClient->getResponse();

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
            'password' => 'Password123',
            'age' => 25,
        ];

        self::$authenticatedClient->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$authenticatedClient->getResponse();

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
            'password' => 'Shor1',
            'age' => 25,
        ];

        self::$authenticatedClient->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], json_encode($payload, JSON_THROW_ON_ERROR));

        $response = self::$authenticatedClient->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testCreateUserWithDuplicatedEmail(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'duplicate@example.com',
            'password' => 'Password123',
            'age' => 25,
        ];

        // Create the first user
        self::$authenticatedClient->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], json_encode($payload));
        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        // Try to create another user with the same email
        self::$authenticatedClient->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], json_encode($payload));
        $response = self::$authenticatedClient->getResponse();
        //        $responseData = \json_decode($response->getContent(), true);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        //        $this->assertEquals('User with email <duplicate@example.com> already exists', $responseData['message']);
    }
}
