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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @doesNotPerformAssertions
 */
abstract class ControllerTestBase extends WebTestCase
{
    use ReloadDatabaseTrait;

    protected const ENDPOINT_USER = '/api/user/%s';
    protected const ENDPOINT_COMPANY = '/api/company/%s';
    protected const ADMIN_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NzM3MDY5NTksImV4cCI6MTY3NTAwMjk1OSwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluQGFwaS5jb20iLCJpZCI6ImU2N2Y4NDczLTgyYTUtNGY4ZS04YjFhLTNkYjUxMGNlMDA3MyJ9.H_tnEC6_B4m2W5k1s4EPe2y5f0USL8uwP-v-2kMcxNBsRE7Qbj1CH5X4_HX_gw6wW2EmPTF2DYuYUPwVQo9u0me2zlcvsfZDbjaG_99dnjrfz0yeDzsDkglbFY9x3sXAGWpJk3c58uyHTI1TiYisn2N8kvVteutAkrLz5TUv2__7OTqOhnjCbnpbWF6k8uvzJBT3HOyxkg_dxX1-KgI_CL8nPmZgsYjeulJGoIamgDaLlghurp3FDufJJKEV1Dnm3Sq5qhKCiDcuTGjuI69Zl38zkql4Lg4Q9JGMEbQJWZYS6YDXbt7f-hY6ZXeL0rfz-0NAy6smXwIVOHA410J7TQ';
    protected const NOT_VALID_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c';
    protected const NON_EXISTING_USER_ID = 'e0a1878f-dd52-4eea-959d-96f589a9f234';
    protected const NON_EXISTING_COMPANY_ID = 'e0a1878f-dd52-4eea-959d-96f589a9f234';
    protected const CREATE_USER_ENDPOINT = '/register';
    protected const CREATE_COMPANY_ENDPOINT = '/company/create';
    protected const ACTIVATE_USER_ENDPOINT = '/api/user/activate';
    protected const CHANGE_USER_PASSWORD_ENDPOINT = '/api/user/change-password';
    protected const ACTIVATE_COMPANY_ENDPOINT = '/company/activate';
    protected const ADD_USER_TO_COMPANY_ENDPOINT = '/company/add-user-to-Company';
    protected const REMOVE_USER_FROM_COMPANY_ENDPOINT = '/company/remove-user-from-Company';
    protected const ENDPOINT_UPDATE_USER = '/api/user';

    protected static ?AbstractBrowser $admin = null;

    protected string $userId;
    protected string $adminId;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        self::$admin = static::createClient();
//        self::$authenticatedClient = static::createClient();

        $admin = User::create(Uuid::random()->value(), 'admin', 'admin@api.com', 'Password1!', 18);
        $password = static::getContainer()->get(PasswordHasherInterface::class)->hashPasswordForUser($admin, 'Password1!');
        $admin->setPassword($password);
        $admin->setRoles(['ROLE_ADMIN']);


        static::getContainer()->get(UserRepositoryInterface::class)->save($admin);

        $jwt = static::getContainer()->get(JWTTokenManagerInterface::class)->create($admin);

        self::$admin->setServerParameters([
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Authorization' => \sprintf('Bearer %s', $jwt)
        ]);

        $this->userId = $this->createUser();
        $this->adminId = $admin->getId();
    }

    // Log in the user and set the token
    protected function logInUser(string $email, string $password): string
    {
        self::$admin->request(Request::METHOD_POST, '/login', [], [], [], \json_encode([
            'email' => $email,
            'password' => $password,
        ]));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new \RuntimeException('Error logging in user');
        }

        return $responseData['token'];
    }

    protected function getResponseData(Response $response): array
    {
        try {
            return \json_decode($response->getContent(), true);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    protected function createUser(): string
    {
        $payload = [
            'name' => 'Peter',
            'email' => 'peter@api.com',
            'password' => 'Fake123',
            'age' => 30,
        ];

//        self::$admin->request(Request::METHOD_POST, '/register', [], [], [], \json_encode($payload));
        self::$admin->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], \json_encode($payload));

        $response = self::$admin->getResponse();

        if (Response::HTTP_CREATED !== $response->getStatusCode()) {
            throw new \RuntimeException('Error creating user');
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

        self::$admin->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], \json_encode($payload));

        $response = self::$admin->getResponse();

        if (Response::HTTP_CREATED !== $response->getStatusCode()) {
            throw new \RuntimeException('Error creating user');
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

        self::$admin->request(Request::METHOD_POST, self::CREATE_COMPANY_ENDPOINT, [], [], [], \json_encode($payload));

        $response = self::$admin->getResponse();

        if (Response::HTTP_CREATED !== $response->getStatusCode()) {
            throw CompanyAlreadyExistsException::createFromTaxPayer($payload['taxpayer']);
        }

        $responseData = $this->getResponseData($response);

        return $responseData['CompanyId'];
    }
}