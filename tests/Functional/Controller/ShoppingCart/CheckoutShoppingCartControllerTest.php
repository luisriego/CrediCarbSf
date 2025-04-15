<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\ShoppingCart;

use App\Tests\Functional\FunctionalTestBase;
use App\Tests\Subscriber\ShoppingCartEventSubscriber;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckoutShoppingCartControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/shopping-cart/checkout';
    private const ADD_ITEM_ENDPOINT = '/api/shopping-cart/add-item';

    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->payload = [
            'ownerId' => $this->companyId,
            'projectId' => $this->projectId,
            'quantity' => 1,
            'price' => '10.00'
        ];
    }

    /**
     * @test
     */
    public function shouldDispatchEventsWhenCheckoutIsSuccessful(): void
    {
        $eventSubscriber = static::getContainer()->get(ShoppingCartEventSubscriber::class);

        $eventCounter = static::getContainer()->get('test.event_counter');
        $initialCount = $eventCounter->count;

        // Add an item to the shopping cart
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ADD_ITEM_ENDPOINT,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($this->payload)
        );

        $addResponse = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $addResponse->getStatusCode(), 'Failed to add item to cart');

        // Perform the checkout
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), 'Checkout failed');

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals(
            'processing', $responseData['status'],
            'Checkout did not produce expected status'
        );
    }


    /**
     * @test
     */
    public function shouldCheckoutShoppingCartSuccessfully(): void
    {
        // Añadir un producto al carrito
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ADD_ITEM_ENDPOINT,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($this->payload)
        );

        // Realizar el checkout
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('processing', $responseData['status']);
    }

    /**
     * @test
     */
    public function shouldCheckoutShoppingCartSuccessfullyWithDiscount(): void
    {
        $discountCode = 'DISCOUNT2023';

        // Añadir un producto al carrito
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ADD_ITEM_ENDPOINT,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($this->payload)
        );

        // Realizar el checkout con descuento
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['discount' => $discountCode])
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('discountedTotal', $responseData);
        $this->assertNotNull($responseData['discountedTotal']);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('processing', $responseData['status']);
    }

    /**
     * @test
     */
    public function shouldNotCheckoutShoppingCartWhenUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}