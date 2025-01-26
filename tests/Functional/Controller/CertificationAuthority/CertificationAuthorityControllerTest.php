<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\CertificationAuthority;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CertificationAuthorityControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/certification-authority/create';

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function shouldCreateCertificationAuthoritySuccessfully(): void
    {
        $payload = [
            'name' => 'Certification Authority Test',
            'website' => 'https://www.certificationauthoritytest.com',
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
        $responseData = \json_decode($response->getContent(), true);
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertArrayHasKey('certificationAuthorityId', $responseData);
    }

    /** @test */
    public function ShouldNotCreateCertificationAuthorityWhenUserUnauthorized(): void
    {
        $payload = [
            'name' => 'Certification Authority Test',
            'website' => 'https://www.certificationauthoritytest.com',
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

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /** @test */
    public function ShouldNotCreateCertificationAuthorityWhenDuplicate(): void
    {
        $payload = [
            'name' => 'Certification Authority Test',
            'website' => 'https://www.certificationauthoritytest.com',
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

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotCreateCertificationAuthorityWithEmptyName(): void
    {
        $payload = [
            'name' => '',
            'website' => 'https://www.certificationauthoritytest.com',
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