<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\CertificationAuthority;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_encode;
use function sprintf;

class UpdateCertificationAuthorityControllerTest extends FunctionalTestBase
{
    protected string $certificationAuthorityId;

    public function setUp(): void
    {
        parent::setUp();
        // Assume $this->certificationAuthorityId is set to a valid ID
    }

    public function testUpdateCertificationAuthoritySuccessfully(): void
    {
        $payload = [
            'name' => 'Updated Certification Authority Name',
            'website' => 'https://updated-website.com',
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('/api/certification-authority/%s', $this->certificationAuthorityId),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testUpdateCertificationAuthorityWithInvalidId(): void
    {
        $payload = [
            'name' => 'Updated Certification Authority Name',
            'website' => 'https://updated-website.com',
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('/api/certification-authority/%s', 'invalid-id'),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testUpdateCertificationAuthorityUnauthorized(): void
    {
        $payload = [
            'name' => 'Updated Certification Authority Name',
            'website' => 'https://updated-website.com',
        ];

        // Simulate an unauthorized user
        self::$baseClient->request(
            Request::METHOD_PATCH,
            sprintf('/api/certification-authority/%s', $this->certificationAuthorityId),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testUpdateCertificationAuthorityWithNullName(): void
    {
        $payload = [
            'name' => null,
            'website' => 'https://updated-website.com',
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('/api/certification-authority/%s', $this->certificationAuthorityId),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testUpdateCertificationAuthorityWithEmptyName(): void
    {
        $payload = [
            'name' => '',
            'website' => 'https://updated-website.com',
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PATCH,
            sprintf('/api/certification-authority/%s', $this->certificationAuthorityId),
            [],
            [],
            [],
            json_encode($payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    //    public function testUpdateCertificationAuthorityWithSameName(): void
    //    {
    //        $payload = [
    //            'name' => 'Existing Certification Authority Name', // Assume this is the current name
    //            'website' => 'https://updated-website.com'
    //        ];
    //
    //        self::$authenticatedClient->request(
    //            Request::METHOD_PATCH,
    //            sprintf('/api/certification-authority/%s', $this->certificationAuthorityId),
    //            [], [], [], json_encode($payload)
    //        );
    //
    //        $response = self::$authenticatedClient->getResponse();
    //        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    //    }
}
