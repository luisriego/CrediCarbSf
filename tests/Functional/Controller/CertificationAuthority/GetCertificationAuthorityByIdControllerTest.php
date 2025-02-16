<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\CertificationAuthority;

use App\Tests\Functional\FunctionalTestBase;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCertificationAuthorityByIdControllerTest extends FunctionalTestBase
{
    protected string $certificationAuthorityId;
    private const ENDPOINT = 'api/certification-authority';

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
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
        $currentId = $this->certificationAuthorityId;
        $lastFourDigits = substr($currentId, -4);
        $shuffledDigits = '';

        $digitsArray = str_split($lastFourDigits);
        while (count($digitsArray) > 0) {
            $index = random_int(0, count($digitsArray) - 1);
            $shuffledDigits .= $digitsArray[$index];
            array_splice($digitsArray, $index, 1);
        }

        $modifiedId = substr($currentId, 0, -4) . $shuffledDigits;
        $this->certificationAuthorityId = $modifiedId;

        $nonExistentId = $modifiedId; // Assume this ID does not exist in the database

        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('/%s/%s', self::ENDPOINT, $nonExistentId),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
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