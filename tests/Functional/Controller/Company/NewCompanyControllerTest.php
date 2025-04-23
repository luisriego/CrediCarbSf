<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Company;

use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\ValueObject\Uuid;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewCompanyControllerTest extends FunctionalTestBase
{
    protected const ENDPOINT_COMPANY = '/api/v1/companies';
    private CompanyRepositoryInterface $companyRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->companyRepository = self::getContainer()->get(CompanyRepositoryInterface::class);
    }

    public function testCreateCompanySuccessfully(): void
    {
        // Enable Symfony's debug mode
        self::$authenticatedClient->enableProfiler();
        
        $payload = [
            'id' => Uuid::random()->value(),
            'taxpayer' => '33592510000154',
            'fantasyName' => 'Test Company',
        ];

        self::$authenticatedClient->request(
            Request::METHOD_PUT,
            self::ENDPOINT_COMPANY,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = self::$authenticatedClient->getResponse();
        
        if ($response->getStatusCode() !== Response::HTTP_CREATED) {
            $content = json_decode($response->getContent(), true);
            $this->fail(sprintf(
                "Request failed: %s\nPayload: %s", 
                $content['message'] ?? 'Unknown error',
                json_encode($payload, JSON_PRETTY_PRINT)
            ));
        }

       // Verify response
       $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
       $this->assertJson($response->getContent());
       $responseData = json_decode($response->getContent(), true);
       $this->assertArrayHasKey('message', $responseData);
       
       // Verify database
       /** @var Company $company */
       $company = $this->companyRepository->findOneByIdOrFail($payload['id']);
       $this->assertNotNull($company);
       $this->assertEquals($payload['taxpayer'], $company->taxpayer());
       $this->assertEquals($payload['fantasyName'], $company->fantasyName());
       $this->assertTrue($company->isActive());
    }
}