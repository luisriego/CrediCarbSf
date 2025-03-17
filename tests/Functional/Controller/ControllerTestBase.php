<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Domain\Exception\Company\CompanyAlreadyExistsException;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Security\PasswordHasherInterface;
use App\Domain\ValueObjects\Uuid;
use Exception;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;
use function json_encode;
use function sprintf;

/**
 * @doesNotPerformAssertions
 */
abstract class ControllerTestBase extends WebTestCase
{
    use ReloadDatabaseTrait;

    protected const ENDPOINT_USER = '/api/user';
    protected const ENDPOINT_COMPANY = '/api/company';
    protected const ADMIN_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MzU5MTY5MTQsImV4cCI6MTczNjI3NjkxNCwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluQGFwaS5jb20iLCJpZCI6IjU1YjcyNzEwLTE4NjctNDIyZS1iYTVmLTgwNzJjODZmNmNjMiJ9.DwjRjDfVaHuPpTr-AILbeefpr5PyC_zK5s38Y2uhYmDSg_WBbRA9us9of7YYNIWg7lBQTIDtTJpDe3O27r-P5NE9LtAcAtHHz4B6nHZXTvhyq4BDTpkwlO_zbZmpcPgwfmq3Cpxx0mqSYanCdOFGDOOmfDJuaCImdm296zSui_CmpBVtiz8Qob04Hbmytx-w1Kbpcju8Un23lwGlYv0j1yt4QMbA3EDSs8PiSJlRi5si6X7JIZ3uJOMigoBtWCRUp2GPOdUXT1xgfYQuagui8lmsOl4y82F1i0ZK1Gm674jj5vCOQvia9EbkBX-L1CRgITmXqrgJdidC3pQ2Yd7TbQ';
    protected const NOT_VALID_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c';
    protected const NON_EXISTING_USER_ID = 'e0a1878f-dd52-4eea-959d-96f589a9f234';
    protected const NON_EXISTING_COMPANY_ID = 'e0a1878f-dd52-4eea-959d-96f589a9f234';
    protected const CREATE_USER_ENDPOINT = '/register';
    protected const CREATE_COMPANY_ENDPOINT = '/api/company/create';
    protected const ACTIVATE_USER_ENDPOINT = '/api/user/activate';
    protected const CHANGE_USER_PASSWORD_ENDPOINT = '/api/user/change-password';
    protected const ACTIVATE_COMPANY_ENDPOINT = '/company/activate';
    protected const ADD_USER_TO_COMPANY_ENDPOINT = '/api/company/adduser';
    protected const REMOVE_USER_FROM_COMPANY_ENDPOINT = '/company/remove-user-from-Company';
    protected const ENDPOINT_UPDATE_USER = '/api/user';

    protected static ?AbstractBrowser $admin = null;

    protected string $companyId;
    protected string $userId;
    protected string $adminId;
    protected string $superAdminId;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        self::$admin = static::createClient();

        $admin = User::create(Uuid::random()->value(), 'admin', 'admin@api.com', 'Password1!', 18);
        $password = static::getContainer()->get(PasswordHasherInterface::class)->hashPasswordForUser($admin, 'Password1!');
        $admin->setPassword($password);
        $admin->setRoles(['ROLE_ADMIN']);

        static::getContainer()->get(UserRepositoryInterface::class)->save($admin);

        $jwt = static::getContainer()->get(JWTTokenManagerInterface::class)->create($admin);

        self::$admin->setServerParameters([
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Authorization' => sprintf('Bearer %s', $jwt),
        ]);

        //        $company = $this->createCompany();
        //        $this->createSuperAdmin();
        $this->userId = $this->createUser(null);
        $this->anotherUser = $this->createAnotherUser(null);
        $this->adminId = $admin->getId();
    }

    // Log in the user and set the token
    protected function logInUser(string $email, string $password): string
    {
        self::$admin->request(Request::METHOD_POST, '/login', [], [], [], json_encode([
            'email' => $email,
            'password' => $password,
        ]));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new RuntimeException('Error logging in user');
        }

        return $responseData['token'];
    }

    protected function getResponseData(Response $response): array
    {
        try {
            return json_decode($response->getContent(), true);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    protected function createSuperAdmin(): void
    {
        $payload = [
            'name' => 'Super Admin',
            'email' => 'super.admin@api.com',
            'password' => 'Fake123!',
            'age' => 40,
        ];

        $superAdmin = User::create(
            Uuid::random()->value(),
            $payload['name'],
            $payload['email'],
            $payload['password'],
            $payload['age'],
        );
        $password = static::getContainer()
            ->get(PasswordHasherInterface::class)
            ->hashPasswordForUser($superAdmin, $payload['password']);
        $superAdmin->setPassword($password);
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);

        static::getContainer()->get(UserRepositoryInterface::class)->save($superAdmin);

        $jwt = static::getContainer()->get(JWTTokenManagerInterface::class)->create($superAdmin);

        self::$admin->setServerParameters([
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Authorization' => sprintf('Bearer %s', $jwt),
        ]);
    }

    /**
     * @throws Exception
     */
    protected function createUser(?string $company): string
    {
        $payload = [
            'name' => 'User',
            'email' => 'user@api.com',
            'password' => 'Fake123!',
            'age' => 30,
        ];

        self::$admin->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$admin->getResponse();

        if (Response::HTTP_CREATED !== $response->getStatusCode()) {
            throw new RuntimeException('Error creating user');
        }

        $responseData = $this->getResponseData($response);

        return $responseData['userId'];
    }

    /**
     * @throws Exception
     */
    protected function createAnotherUser(): string
    {
        $payload = [
            'name' => 'Juan',
            'email' => 'juan@api.com',
            'password' => 'Fake123',
            'age' => 38,
        ];

        self::$admin->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$admin->getResponse();

        if (Response::HTTP_CREATED !== $response->getStatusCode()) {
            throw new RuntimeException('Error creating user');
        }

        $responseData = $this->getResponseData($response);

        return $responseData['userId'];
    }

    /**
     * @throws Exception
     */
    protected function createCompany(): string
    {
        //        $userId = $this->createUser();

        $payload = [
            'fantasyName' => 'Company Fake',
            'taxpayer' => '02024517000146',
        ];

        self::$admin->request(Request::METHOD_POST, self::CREATE_COMPANY_ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$admin->getResponse();

        if (Response::HTTP_CREATED !== $response->getStatusCode()) {
            throw CompanyAlreadyExistsException::createFromTaxPayer($payload['taxpayer']);
        }

        $responseData = $this->getResponseData($response);

        return $responseData['CompanyId'];
    }

    /**
     * @throws Exception
     */
    protected function createNewCompany(string $fantasyName, string $taxpayer): void
    {
        $payload = [
            'fantasyName' => $fantasyName,
            'taxpayer' => $taxpayer,
        ];

        self::$admin->request(
            Request::METHOD_POST,
            self::CREATE_COMPANY_ENDPOINT,
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$admin->getResponse();
        //
        //        if (Response::HTTP_CREATED !== $response->getStatusCode()) {
        //            throw CompanyAlreadyExistsException::createFromTaxPayer($payload['taxpayer']);
        //        }

        $responseData = $this->getResponseData($response);

        //        return $responseData['CompanyId'];
    }

    protected function addUserToCompany(string $userId, string $companyId): void
    {
        $payload = [
            'userId' => $userId,
            'companyId' => $companyId,
        ];

        self::$admin->request(Request::METHOD_POST, self::ADD_USER_TO_COMPANY_ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$admin->getResponse();

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new RuntimeException('Error adding user to company');
        }
    }
}
