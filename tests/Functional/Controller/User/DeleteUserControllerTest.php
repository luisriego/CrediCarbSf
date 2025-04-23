<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Domain\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Password;
use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\UserName;
use App\Tests\Functional\FunctionalTestBase;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function sprintf;

class DeleteUserControllerTest extends FunctionalTestBase
{
    protected string $userId;

    public function setUp(): void
    {
        parent::setUp();

        $userRepository = static::getContainer()->get(UserRepositoryInterface::class);
        $testUser = User::create(
            UserId::random(), 
            UserName::fromString('For Testing'),
            Email::fromString('for@testing.app'), 
            Password::fromString('Password1!'));
        $userRepository->save($testUser, true);
        $this->userId = $testUser->getId();

    }

    public function testDeleteUserSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->userId),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteNonExistingUser(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, self::NON_EXISTING_USER_ID),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testDeleteUserWithoutPermission(): void
    {
        self::$baseClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, $this->adminId),
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testDeleteUserWithInvalidId(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf('%s/%s', self::ENDPOINT_USER, 'invalid-id'),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
