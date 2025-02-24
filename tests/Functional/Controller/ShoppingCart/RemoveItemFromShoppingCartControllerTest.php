<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\ShoppingCart;

use App\Domain\Repository\ShoppingCartRepositoryInterface;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveItemFromShoppingCartControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/shopping-cart/remove-item/';
    private const ENDPOINT_ADD_ITEM = '/api/shopping-cart/add-item';

    public function setUp(): void
    {
        parent::setUp();
        $this->addItemToShoppingCart();

        $this->shoppingCartRepository = self::getContainer()->get(ShoppingCartRepositoryInterface::class);
//
//        // Define the payload for adding an item
        $this->payload = [
            'ownerId' => $this->companyId,
            'projectId' => $this->projectId,
            'quantity' => 1,
            'price' => '10.00',
        ];
    }

    /** @test */
    public function shouldRemoveItemFromShoppingCartSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT_ADD_ITEM,
            [],
            [],
            [],
            \json_encode($this->payload)
        );

        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            self::ENDPOINT . $this->shoppingCartItemId
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = \json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertArrayHasKey('shoppingCartId', $responseData);
        self::assertArrayHasKey('itemIds', $responseData);
        self::assertNotContains($this->shoppingCartItemId, $responseData['itemIds']);
    }

    /** @test */
    public function shouldNotRemoveItemFromShoppingCartWhenUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_DELETE,
            self::ENDPOINT . $this->shoppingCartItemId
        );

        $response = self::$baseClient->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotRemoveItemFromShoppingCartWithInvalidItemId(): void
    {
        $invalidItemId = 'invalid-uid';

        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            self::ENDPOINT . $invalidItemId
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}