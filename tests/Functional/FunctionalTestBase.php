<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Domain\Model\Company;
use App\Domain\Repository\CertificationAuthorityRepositoryInterface;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\ProjectRepositoryInterface;
use App\Domain\Repository\ShoppingCartRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;
use function json_encode;
use function sprintf;

class FunctionalTestBase extends WebTestCase
{
    use ReloadDatabaseTrait;

    protected const ACTIVATE_USER_ENDPOINT = '/api/user/activate';
    protected const ADD_USER_TO_COMPANY_ENDPOINT = '/api/company/adduser';
    protected const CHANGE_USER_PASSWORD_ENDPOINT = '/api/user/change-password';
    protected const CREATE_COMPANY_ENDPOINT = '/api/company/create';
    protected const CREATE_USER_ENDPOINT = '/register';
    protected const ENDPOINT_COMPANY = '/api/company';
    protected const ENDPOINT_USER = '/api/user';
    protected const NON_EXISTING_COMPANY_ID = 'e0a1878f-dd52-4eea-959d-96f589a9f234';
    protected const NON_EXISTING_USER_ID = 'e0a1878f-dd52-4eea-959d-96f589a9f234';
    protected static ?KernelBrowser $baseClient = null;
    protected static ?KernelBrowser $authenticatedClient = null;
    protected static ?KernelBrowser $anotherAuthenticatedClient = null;
    protected static ?KernelBrowser $superAdminClient = null;

    protected string $companyId;
    protected Company $company;
    protected string $projectId;
    protected string $userId;
    protected string $adminId;
    protected string $shoppingCartId;
    protected string $shoppingCartItemId;

    private static ?KernelBrowser $client = null;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        if (null === self::$client) {
            self::$client = static::createClient();
        }

        if (null === self::$baseClient) {
            self::$baseClient = clone self::$client;
            self::$baseClient->setServerParameters([
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ]);
        }

        if (null === self::$authenticatedClient) {
            self::$authenticatedClient = clone self::$client;

            $user = static::getContainer()->get(UserRepositoryInterface::class)->findOneByEmail('admin@api.com');
            $token = static::getContainer()->get(JWTTokenManagerInterface::class)->create($user);

            self::$authenticatedClient->setServerParameters([
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_Authorization' => sprintf('Bearer %s', $token),
            ]);
        }

        if (null === self::$anotherAuthenticatedClient) {
            self::$anotherAuthenticatedClient = clone self::$client;

            $this->createUser('user', 'user@api.com');
            $user = static::getContainer()->get(UserRepositoryInterface::class)->findOneByEmail('user@api.com');
            $token = static::getContainer()->get(JWTTokenManagerInterface::class)->create($user);

            self::$anotherAuthenticatedClient->setServerParameters([
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_Authorization' => sprintf('Bearer %s', $token),
            ]);
        }

        if (null === self::$superAdminClient) {
            self::$superAdminClient = clone self::$client;

            $user = static::getContainer()->get(UserRepositoryInterface::class)->findOneByEmail('superadmin@api.com');
            $token = static::getContainer()->get(JWTTokenManagerInterface::class)->create($user);

            self::$superAdminClient->setServerParameters([
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_Authorization' => sprintf('Bearer %s', $token),
            ]);
        }

        $company = static::getContainer()->get(CompanyRepositoryInterface::class)->findOneBy(['taxpayer' => '33592510015500']);
        $project = static::getContainer()->get(ProjectRepositoryInterface::class)->findOneBy(['name' => 'Project 2']);
        $authority = static::getContainer()->get(CertificationAuthorityRepositoryInterface::class)->findOneBy(['taxpayer' => '48846500000175']);
        $admin = static::getContainer()->get(UserRepositoryInterface::class)->findOneByEmail('admin@api.com');
        $user = static::getContainer()->get(UserRepositoryInterface::class)->findOneByEmail('user@api.com');
        $shoppingCart = static::getContainer()->get(ShoppingCartRepositoryInterface::class)->findOneBy(['owner' => $company]);
        $repo = static::getContainer()->get(\App\Domain\Repository\UserRepositoryInterface::class);
        $this->adminId = $admin->getId();
        $this->userId = $user->getId();
        $this->companyId = $company->getId();
        $this->company = $company;
        $this->projectId = $project->getId();
        $this->certificationAuthorityId = $authority->getId();
        $this->shoppingCartId = $shoppingCart->getId();
    }

    protected function createUser(string $name, string $email): void
    {
        $payload = [
            'name' => $name,
            'email' => $email,
            'password' => 'Fake123!',
            'age' => 30,
        ];

        self::$client->request(Request::METHOD_POST, self::CREATE_USER_ENDPOINT, [], [], [], json_encode($payload));
    }

    protected function createNewCompany(string $fantasyName, string $taxpayer): void
    {
        $payload = [
            'fantasyName' => $fantasyName,
            'taxpayer' => $taxpayer,
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::CREATE_COMPANY_ENDPOINT,
            [],
            [],
            [],
            json_encode($payload),
        );
    }

    protected function addItemToShoppingCart(): void
    {
        $payload = [
            'ownerId' => $this->companyId,
            'projectId' => $this->projectId,
            'quantity' => 1,
            'price' => '10.00',
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            '/api/shopping-cart/add-item',
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $this->shoppingCartId = $responseData['shoppingCartId'];
        $this->shoppingCartItemId = $responseData['itemIds'][0]['id'];
    }

    protected function purgeDatabase(): void
    {
        $connection = self::$kernel->getContainer()->get('doctrine')->getConnection();

        // Disable foreign key checks
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'shopping_cart_item',
            'shopping_cart',
            'project',
            'company',
            // Add other tables in correct order
        ];

        foreach ($tables as $table) {
            $connection->executeQuery("TRUNCATE TABLE `{$table}`");
        }

        // Re-enable foreign key checks
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
    }

    protected static function initDBConnection(): Connection
    {
        if (null === static::$kernel) {
            static::bootKernel();
        }

        return static::$kernel->getContainer()->get('doctrine')->getConnection();
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
    protected function getAdminId()
    {
        return self::initDBConnection()->executeQuery('SELECT id FROM user WHERE email = "admin@api.com"')->fetchOne();
    }

    /**
     * @throws Exception
     */
    protected function getUserId()
    {
        return self::initDBConnection()->executeQuery('SELECT id FROM user WHERE email = "user@api.com"')->fetchOne();
    }

    /**
     * @throws Exception
     */
    protected function getCompanyValeId()
    {
        return self::initDBConnection()->executeQuery('SELECT id FROM company WHERE taxpayer = "33592510015500"')->fetchOne();
    }
}
