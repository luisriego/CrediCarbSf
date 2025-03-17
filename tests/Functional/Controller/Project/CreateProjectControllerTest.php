<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Tests\Functional\FunctionalTestBase;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function array_merge;
use function json_decode;
use function json_encode;

use const JSON_THROW_ON_ERROR;

class CreateProjectControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/project/create';
    private const PROJECT_NAME = 'Project Test';
    private const PROJECT_NAME_LIKE = 'Test Project';
    private const PROJECT_DESCRIPTION = 'Description Test';
    private const PROJECT_AREA = '100.00';
    private const PROJECT_QUANTITY = '50.00';
    private const PROJECT_PRICE = '10.00';
    private const PROJECT_PRICE_LIKE = '20.00';
    private const PROJECT_TYPE = 'REFORESTATION';

    /** @test
     * @throws JsonException
     */
    public function shouldCreateProjectSuccessfully(): void
    {
        $payload = $this->getPayload();

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertArrayHasKey('projectId', $responseData);
    }

    /** @test
     * @throws JsonException
     */
    public function shouldNotCreateProjectWhenUserUnauthorized(): void
    {
        $payload = $this->getPayload(['price' => self::PROJECT_PRICE_LIKE]);

        self::$baseClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );

        $response = self::$baseClient->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /** @test
     * @throws JsonException
     */
    public function shouldNotCreateProjectWhenDuplicate(): void
    {
        $payload = $this->getPayload();

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /** @test
     * @throws JsonException
     */
    public function shouldNotCreateProjectWhenDuplicateLikeName(): void
    {
        $payload = $this->getPayload();
        $payloadLike = $this->getPayload(['name' => self::PROJECT_NAME_LIKE]);

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($payloadLike, JSON_THROW_ON_ERROR),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /** @test
     * @throws JsonException
     */
    public function shouldNotCreateProjectWhenInvalidData(): void
    {
        $payload = $this->getPayload([
            'name' => 'Pr',  // name too short
            'description' => '',
            'areaHa' => 'invalid',
            'quantity' => 'invalid',
            'price' => 'invalid',
            'projectType' => 'InvalidType',
        ]);

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    private function getPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => self::PROJECT_NAME,
            'description' => self::PROJECT_DESCRIPTION,
            'areaHa' => self::PROJECT_AREA,
            'quantity' => self::PROJECT_QUANTITY,
            'price' => self::PROJECT_PRICE,
            'projectType' => self::PROJECT_TYPE,
            'owner' => $this->companyId,
        ], $overrides);
    }
}
