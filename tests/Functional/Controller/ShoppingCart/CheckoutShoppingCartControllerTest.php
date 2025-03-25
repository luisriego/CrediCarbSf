<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\ShoppingCart;

use App\Domain\Event\ShoppingCartCheckedOut;
use App\Tests\Functional\FunctionalTestBase;
use JsonException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;
use function json_encode;

use const JSON_THROW_ON_ERROR;

class CheckoutShoppingCartControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/shopping-cart/checkout';

    /**
     * @test
     */
    public function shouldDispatchEventsWhenCheckoutIsSuccessful(): void
    {
        // Get the event counter from the container
        $eventCounter = static::getContainer()->get('test.event_counter');
        $eventCounter->count = 0;
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        // Check directly if the counter increased
        $this->assertGreaterThan(0, $eventCounter->count, 'No ShoppingCartCheckedOut events has been detected');
    }

    /**
     * @test
     * @throws JsonException
     */
    public function shouldCheckoutShoppingCartSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('processing', $responseData['status']);

    }

    /**
     * @test
     * @throws JsonException
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
            json_encode(['discountCode' => $discountCode], JSON_THROW_ON_ERROR),
        );

        $response = self::$authenticatedClient->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
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
        );

        $response = self::$baseClient->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
