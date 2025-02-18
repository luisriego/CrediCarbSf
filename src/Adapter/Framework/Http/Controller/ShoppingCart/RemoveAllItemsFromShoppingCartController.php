<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\ShoppingCart;

use App\Application\UseCase\ShoppingCart\RemoveAllItemsFromShoppingCartService\Dto\RemoveAllItemsFromShoppingCartInputDto;
use App\Application\UseCase\ShoppingCart\RemoveAllItemsFromShoppingCartService\RemoveAllItemsFromShoppingCartService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RemoveAllItemsFromShoppingCartController
{
    public function __construct(
        private readonly RemoveAllItemsFromShoppingCartService $removeAllItemsFromShoppingCartService,
    ) {}

    #[Route(
        '/api/shopping-cart/{shoppingCartId}/remove-all-items',
        name: 'shopping_cart_remove_all_items',
        methods: ['DELETE'],
    )]
    #[IsGranted('ROLE_USER')]
    public function __invoke(string $shoppingCartId): Response
    {
        $this->removeAllItemsFromShoppingCartService->handle(
            RemoveAllItemsFromShoppingCartInputDto::create(
                $shoppingCartId,
            ),
        );

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
