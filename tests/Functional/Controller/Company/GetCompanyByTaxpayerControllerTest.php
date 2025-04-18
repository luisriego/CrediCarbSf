<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Domain\Repository\CompanyRepositoryInterface;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function sprintf;

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
}
