<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;
use function json_encode;
use function sprintf;

class UpdateUserControllerTest extends FunctionalTestBase
{
    protected string $userId;

    public function setUp(): void
    {
        parent::setUp();
        //        $authenticatedClientUser = static::getContainer()->get(UserRepositoryInterface::class)->findOneByEmail('admin@api.com');
        //        $this->userId = $authenticatedClientUser->getId();
    }

    public function testUpdateUserSuccessfully(): void
    {
        $payload = [
            'name' => 'Updated Name',
            'age' => 25,
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->userId),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testUpdateUserWithInvalidAge(): void
    {
        $payload = [
            'name' => 'Updated Name',
            'age' => 10, // Invalid age, assuming minimum age is 18
            'company' => null,
            'keys' => ['name', 'age'],
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->userId),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('Age has to be at least 18', $responseData['message']);
    }

    public function testUpdateUserWithImmutableField(): void
    {
        $payload = [
            'email' => 'newemail@example.com', // Email is immutable
            'keys' => ['email'],
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->userId),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('Cannot update email because it is immutable', $responseData['message']);
    }

    public function testUpdateUserWithNonExistingUser(): void
    {
        $payload = [
            'name' => 'Updated Name',
            'age' => 25,
            'company' => null,
            'keys' => ['name', 'age'],
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_USER, self::NON_EXISTING_USER_ID),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testUpdateUserWithInvalidCompany(): void
    {
        $payload = [
            'name' => 'Updated Name',
            'age' => 25,
            'company' => self::NON_EXISTING_COMPANY_ID, // Non-existing company
            'keys' => ['name', 'age', 'company'],
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->userId),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals(sprintf('Resource of type [%s] with ID [%s] not found', 'App\Domain\Model\Company', 'e0a1878f-dd52-4eea-959d-96f589a9f234'), $responseData['message']);
    }
}
