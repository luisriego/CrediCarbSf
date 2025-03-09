<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\ShoppingCart;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckoutShoppingCartControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/shopping-cart/checkout';

    /** @test */
    public function shouldCheckoutShoppingCartSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    /**
     * @throws \JsonException
     */
    public function shouldCheckoutShoppingCartSuccessfullyWithDiscount(): void
    {
        $discountCode = 'DISCOUNT2023'; // Example discount code

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['discountCode' => $discountCode], JSON_THROW_ON_ERROR)
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey('discountedTotal', $responseData);
        $this->assertNotNull($responseData['discountedTotal']);
    }

    /** @test */
    public function shouldNotCheckoutShoppingCartWhenUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_POST,
            self::ENDPOINT
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}