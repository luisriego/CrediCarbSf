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

        // Add first item
        $this->addItemToShoppingCart();

        // Add second item
        $this->addItemToShoppingCart();
    }

    /** @test */
    public function removeAllItemsFromCartSuccessfully(): void
    {
        // Verify the cart currently has items
        $shoppingCart = $this->shoppingCartRepository->findOneByIdOrFail($this->shoppingCartId);
        self::assertCount(2, $shoppingCart->getItems());

        self::$authenticatedClient->request(
            Request::METHOD_DELETE,
            sprintf(self::ENDPOINT, $this->shoppingCartId)
        );

        $response = self::$authenticatedClient->getResponse();
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        // Clear to ensure fresh data
        self::getContainer()->get('doctrine')->getManager()->clear();

        // Expect the cart to be removed
        $this->expectException(\App\Domain\Exception\ResourceNotFoundException::class);
        $this->shoppingCartRepository->findOneByIdOrFail($this->shoppingCartId);
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