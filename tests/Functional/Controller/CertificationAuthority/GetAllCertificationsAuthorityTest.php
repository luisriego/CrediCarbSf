<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\CertificationAuthority;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetAllCertificationsAuthorityTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/certification-authority/all';

    /** @test */
    public function shouldGetAllCertificationsAuthoritySuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            self::ENDPOINT
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data['certificationAuthorities']);
    }

    /** @test */
    public function shouldNotGetAllCertificationsAuthorityWhenUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_GET,
            self::ENDPOINT
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

//    /** @test */
//    public function shouldGetAllCertificationsAuthorityWithNoData(): void
//    {
//        // Assuming the database is empty or the repository returns an empty array
//        self::$authenticatedClient->request(
//            Request::METHOD_GET,
//            self::ENDPOINT
//        );
//
//        $response = self::$authenticatedClient->getResponse();
//        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
//
//        $data = json_decode($response->getContent(), true);
//        $this->assertEmpty($data['certificationAuthorities']);
//    }
}