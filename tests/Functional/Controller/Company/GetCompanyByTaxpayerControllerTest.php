<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Domain\Repository\CompanyRepositoryInterface;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCompanyByTaxpayerControllerTest extends FunctionalTestBase
{
    protected string $companyTaxpayer;

    public function setUp(): void
    {
        parent::setUp();
        $company = static::getContainer()->get(CompanyRepositoryInterface::class)->findOneByTaxpayer('33592510015500');
        $this->companyTaxpayer = $company->taxPayer();
    }

    public function testGetCompanyByTaxpayerSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyTaxpayer),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testGetCompanyByNonExistingTaxpayer(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, '33592510000133'),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetCompanyByTaxpayerWithoutPermission(): void
    {
        // Simulate an unauthorized user
        self::$baseClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, $this->companyTaxpayer),
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testGetCompanyByInvalidTaxpayer(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf('%s/%s', self::ENDPOINT_COMPANY, '1234567890123456789'),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}