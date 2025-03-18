<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\ShoppingCart;

use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function array_column;
use function json_decode;
use function json_encode;

class AddItemToShoppingCartControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/shopping-cart/add-item';
    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        // Define the payload for adding an item
        $this->payload = [
            'ownerId' => $this->companyId,
            'projectId' => $this->projectId,
            'quantity' => 1,
            'price' => '10.00',
        ];
    }

    /**
     * @test
     */
    public function shouldAddItemToShoppingCartSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($this->payload),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertArrayHasKey('shoppingCartId', $responseData);
        self::assertArrayHasKey('itemIds', $responseData);
        $projectIds = array_column(array_column($responseData['itemIds'], 'project'), 'id');
        self::assertContains($this->payload['projectId'], $projectIds);
    }

    /**
     * @test
     */
    public function shouldNotAddItemToShoppingCartWhenUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($this->payload),
        );

        $response = self::$baseClient->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldNotAddItemToShoppingCartWithInvalidProjectId(): void
    {
        $this->payload['projectId'] = 'invalid-uid';

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($this->payload),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldNotAddItemToShoppingCartWithMissingFields(): void
    {
        unset($this->payload['projectId']);

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($this->payload),
        );

        $this->payload['projectId'] = $this->projectId;

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldNotAddItemToShoppingCartWithNegativeQuantity(): void
    {
        $this->payload['quantity'] = -1;

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($this->payload),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldNotAddItemToShoppingCartWithInvalidPriceFormat(): void
    {
        $this->payload['price'] = 'invalid-price';

        self::$authenticatedClient->request(
            Request::METHOD_POST,
            self::ENDPOINT,
            [],
            [],
            [],
            json_encode($this->payload),
        );

        $response = self::$authenticatedClient->getResponse();

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
