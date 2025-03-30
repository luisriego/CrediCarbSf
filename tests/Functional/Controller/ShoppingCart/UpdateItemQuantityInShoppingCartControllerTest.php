<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\ShoppingCart;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;
use function json_encode;
use function sprintf;

class UpdateItemQuantityInShoppingCartControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/shopping-cart/%s/quantity';

    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

//        $this->addItemToShoppingCart();

        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager->beginTransaction();


        // Define the payload for updating item quantity
        $this->payload = [
            'shoppingCartId' => $this->shoppingCartId,
            'itemId' => $this->shoppingCartItemId,
            'quantity' => 2,
        ];
    }

    protected function tearDown(): void
    {
        // Roll back the transaction to restore the database state
        $this->entityManager->rollback();
        parent::tearDown();
    }


    /**
     * @test
     * @throws \JsonException
     */
    public function shouldUpdateItemQuantityInShoppingCartSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_PUT,
            sprintf(self::ENDPOINT, $this->shoppingCartId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['itemId' => $this->shoppingCartItemId, 'quantity' => 2], JSON_THROW_ON_ERROR),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertArrayHasKey('shoppingCartId', $responseData);
        self::assertArrayHasKey('itemIds', $responseData);
        self::assertContains($this->payload['itemId'], $responseData['itemIds']);

        self::assertEquals(4, $responseData['itemIds']['quantity']);
    }

    /**
     * @test
     */
    public function shouldNotUpdateItemQuantityInShoppingCartWhenUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_PUT,
            sprintf(self::ENDPOINT, $this->shoppingCartId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['itemId' => $this->shoppingCartItemId, 'quantity' => 2]),
        );

        $response = self::$baseClient->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldNotUpdateItemQuantityInShoppingCartWithInvalidItemId(): void
    {
        $invalidItemId = 'invalid-item-id';

        self::$authenticatedClient->request(
            Request::METHOD_PUT,
            sprintf(self::ENDPOINT, $this->shoppingCartId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['itemId' => $invalidItemId, 'quantity' => 2]),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldNotUpdateItemQuantityInShoppingCartWithNegativeQuantity(): void
    {
        $this->payload['quantity'] = -1;

        self::$authenticatedClient->request(
            Request::METHOD_PUT,
            sprintf(self::ENDPOINT, $this->payload['shoppingCartId'], $this->payload['itemId']),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['itemId' => $this->shoppingCartItemId, 'quantity' => -2]),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
