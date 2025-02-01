<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\CertificationAuthority;

use App\Domain\Model\CertificationAuthority;
use App\Domain\Repository\CertificationAuthorityRepositoryInterface;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteCertificationAuthorityControllerTest extends FunctionalTestBase
{
    protected string $certificationAuthorityId;
    private const ENDPOINT = '/api/certification-authority/{id}';

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function shouldDeleteCertificationAuthoritySuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf('/api/certification-authority/%s', $this->certificationAuthorityId),
        );

        $response = self::$authenticatedClient->getResponse();

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertFalse($this->findCertificationAuthorityById($this->certificationAuthorityId));
    }

    /** @test */
    public function shouldNotDeleteCertificationAuthorityWhenUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_DELETE,
            sprintf('/api/certification-authority/%s', $this->certificationAuthorityId),
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /** @test */
    public function shouldReturnNotFoundWhenCertificationAuthorityDoesNotExist(): void
    {
        $nonExistentId = '22445624-1030-4686-bb03-7f784f7ed410'; // Assume this ID does not exist in the database

        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            str_replace('{id}', (string)$nonExistentId, self::ENDPOINT)
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    private function findCertificationAuthorityById(string $id): bool
    {
        self::$authenticatedClient->request(Request::METHOD_GET,
            sprintf('/api/certification-authority/%s', $this->certificationAuthorityId),
            [], [], ['CONTENT_TYPE' => 'application/json']
        );

        $response = self::$authenticatedClient->getResponse();
        return $response->getStatusCode() === Response::HTTP_OK;
    }
}