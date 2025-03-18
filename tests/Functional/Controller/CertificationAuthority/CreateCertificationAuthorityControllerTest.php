<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\CertificationAuthority;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;
use function json_encode;

class CreateCertificationAuthorityControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/certification-authority/create';
    private array $payload = [
        'taxpayer' => '48.846.500/0001-75',
        'name' => 'Certification Authority Test',
        'website' => 'https://www.certificationauthoritytest.com',
    ];

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function shouldCreateCertificationAuthoritySuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($this->payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertArrayHasKey('certificationAuthorityId', $responseData);
    }

    /**
     * @test
     */
    public function shouldNotCreateCertificationAuthorityWhenUserUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($this->payload),
        );

        $response = self::$baseClient->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldNotCreateCertificationAuthorityWhenDuplicate(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($this->payload),
        );

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($this->payload),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldNotCreateCertificationAuthorityWithEmptyTaxpayer(): void
    {
        $payload = [
            'taxpayer' => '',
            'name' => 'Certification Authority Test',
            'website' => 'https://www.certificationauthoritytest.com',
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldNotCreateCertificationAuthorityWithEmptyName(): void
    {
        $payload = [
            'taxpayer' => '48.846.500/0001-75',
            'name' => '',
            'website' => 'https://www.certificationauthoritytest.com',
        ];

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
