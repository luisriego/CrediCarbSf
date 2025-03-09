<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\ShoppingCart;

use App\Tests\Functional\FunctionalTestBase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewCartSummaryControllerTest extends FunctionalTestBase
{
    use RefreshDatabaseTrait;
    private const ENDPOINT = '/api/shopping-cart';

    function setUp(): void
    {
        parent::setUp();

//        // Load the fixtures
//        $this->loadFixtures([
//            'fixtures/ShoppingCart.yaml',
//            'fixtures/ShoppingCartItem.yaml',
//        ]);
    }

    /** @test */
    public function shouldViewCartSummarySuccessfully(): void
    {
        $this->addItemToShoppingCart();

        self::$authenticatedClient->request(
            Request::METHOD_GET,
            self::ENDPOINT
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = \json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertArrayHasKey('items', $responseData);
        self::assertArrayHasKey('total', $responseData);
    }

    /** @test */
    public function shouldNotViewCartSummaryWhenUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_GET,
            self::ENDPOINT
        );

        $response = self::$baseClient->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}