<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\CertificationAuthority;

use App\Tests\Functional\FunctionalTestBase;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

use function array_splice;
use function count;
use function json_decode;
use function random_int;
use function sprintf;

class GetCertificationAuthorityByIdControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = 'api/v1/certification-authorities';
    protected string $certificationAuthorityId;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function shouldGetCertificationAuthorityByIdSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('/%s/%s', self::ENDPOINT, $this->certificationAuthorityId),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('CertificationAuthority', $responseData);
        $this->assertEquals($this->certificationAuthorityId, $responseData['CertificationAuthority']['id']);
    }

    /** @test
     * @throws RandomException
     */
    public function shouldReturnNotFoundWhenCertificationAuthorityDoesNotExist(): void
    {
        $nonExistentId =  Uuid::v4()->toRfc4122();

        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('/%s/%s', self::ENDPOINT, $nonExistentId),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldReturnForbiddenWhenUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_GET,
            sprintf('/%s/%s', self::ENDPOINT, $this->certificationAuthorityId),
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
