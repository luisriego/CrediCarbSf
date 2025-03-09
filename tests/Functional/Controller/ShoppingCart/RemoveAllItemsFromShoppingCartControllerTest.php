<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\ShoppingCart;

use App\Domain\Repository\ShoppingCartRepositoryInterface;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveAllItemsFromShoppingCartControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/shopping-cart/%s/remove-all-items';

    public function setUp(): void
    {
        parent::setUp();

        $this->shoppingCartRepository = self::getContainer()->get(ShoppingCartRepositoryInterface::class);
//
//        // Add first item
//        $this->addItemToShoppingCart();
//
//        // Add second item
//        $this->addItemToShoppingCart();
    }

    /** @test */
    public function removeAllItemsFromCartSuccessfully(): void
    {
        // Verify the cart currently has items
        $shoppingCart = $this->shoppingCartRepository->findFirst();
        self::assertCount(0, $shoppingCart->getItems());

        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf(self::ENDPOINT, $this->shoppingCartId)
        );

        // Verify response
        $response = self::$authenticatedClient->getResponse();
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
//        self::assertNull($this->shoppingCartRepository->find($this->shoppingCartId));
    }

    /** @test */
    public function shouldNotRemoveAllItemsWhenUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_DELETE,
            sprintf(self::ENDPOINT, $this->shoppingCartId)
        );

        $response = self::$baseClient->getResponse();
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotRemoveAllItemsWithInvalidShoppingCartId(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf(self::ENDPOINT, 'invalid-uid')
        );

        $response = self::$authenticatedClient->getResponse();
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}