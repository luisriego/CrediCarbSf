<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Domain\Repository\ProjectRepositoryInterface;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateProjectControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/project/create';
    private const PROJECT_NAME = 'Project Test';
    private const PROJECT_DESCRIPTION = 'Description Test';
    private const PROJECT_AREA = '100.00';
    private const PROJECT_QUANTITY = '50.00';
    private const PROJECT_PRICE = '10.00';
    private const PROJECT_TYPE = 'Reforestation';

    private ProjectRepositoryInterface $projectRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->projectRepository = $this->getContainer()->get(ProjectRepositoryInterface::class);
    }

    /** @test */
    public function shouldCreateProjectSuccessfully(): void
    {
        $payload = [
            'name' => self::PROJECT_NAME,
            'description' => self::PROJECT_DESCRIPTION,
            'areaHa' => self::PROJECT_AREA,
            'quantity' => self::PROJECT_QUANTITY,
            'price' => self::PROJECT_PRICE,
            'projectType' => self::PROJECT_TYPE
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = $this->getResponseData($response);

        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertArrayHasKey('projectId', $responseData);
        self::assertNotEmpty($responseData['projectId']);
    }

    /** @test */
    public function shouldNotCreateProjectWhenUserUnauthorized(): void
    {
        $payload = [
            'name' => self::PROJECT_NAME,
            'description' => self::PROJECT_DESCRIPTION,
            'areaHa' => self::PROJECT_AREA,
            'quantity' => self::PROJECT_QUANTITY,
            'price' => self::PROJECT_PRICE,
            'projectType' => self::PROJECT_TYPE
        ];

        self::$baseClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$baseClient->getResponse();

        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotCreateProjectWhenDuplicate(): void
    {
        $payload = [
            'name' => self::PROJECT_NAME,
            'description' => self::PROJECT_DESCRIPTION,
            'areaHa' => self::PROJECT_AREA,
            'quantity' => self::PROJECT_QUANTITY,
            'price' => self::PROJECT_PRICE,
            'projectType' => self::PROJECT_TYPE
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            \json_encode($payload)
        );

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotCreateProjectWhenInvalidData(): void
    {
        $payload = [
            'name' => 'Pr',  // nome muito curto
            'description' => '',
            'areaHa' => 'invalid',
            'quantity' => 'invalid',
            'price' => 'invalid',
            'projectType' => 'InvalidType'
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}