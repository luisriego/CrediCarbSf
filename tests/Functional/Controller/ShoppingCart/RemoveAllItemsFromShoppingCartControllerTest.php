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
    private const ENDPOINT_ADD_ITEM = '/api/shopping-cart/add-item';
    private ShoppingCartRepositoryInterface $shoppingCartRepository;
    private string $shoppingCartId;
    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->shoppingCartRepository = self::getContainer()->get(ShoppingCartRepositoryInterface::class);

        // Define the payload for adding an item
        $this->payload = [
            'ownerId' => $this->companyId,
            'projectId' => $this->projectId,
            'quantity' => 1,
            'price' => '10.00',
        ];

        // Add first item
        $this->addItemToShoppingCart();

        // Add second item with a different project ID
//        $this->payload['projectId'] = $this->createProject();
        $this->addItemToShoppingCart();

        // Wait for the database to be updated
        usleep(100000); // 100ms delay
    }

    private function addItemToShoppingCart(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT_ADD_ITEM,
            [],
            [],
            [],
            \json_encode($this->payload)
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = \json_decode($response->getContent(), true);

        if (!isset($this->shoppingCartId)) {
            $this->shoppingCartId = $responseData['shoppingCartId'];
        }
    }

    /** @test */
    public function shouldRemoveAllItemsFromShoppingCartSuccessfully(): void
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