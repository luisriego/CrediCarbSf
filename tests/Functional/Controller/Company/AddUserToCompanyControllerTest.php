<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Tests\Functional\FunctionalTestBase;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_encode;
use function sprintf;

class AddUserToCompanyControllerTest extends FunctionalTestBase
{
    protected string $companyId;
    protected string $userId;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAddUserToCompanySuccessfully(): void
    {
        $payload = [
            'userId' => $this->adminId,
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            sprintf(
                '%s/%s',
                self::ADD_USER_TO_COMPANY_ENDPOINT,
                $this->companyId,
            ),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }
}
