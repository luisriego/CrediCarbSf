<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\ShoppingCart;

use App\Application\UseCase\ShoppingCart\RemoveItemFromShoppingCartService\Dto\RemoveItemFromShoppingCartInputDto;
use App\Application\UseCase\ShoppingCart\RemoveItemFromShoppingCartService\RemoveItemFromShoppingCartService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RemoveItemFromShoppingCartController
{
    public function __construct(
        private readonly RemoveItemFromShoppingCartService $removeItemFromShoppingCartService,
    ) {}

    #[Route('/api/shopping-cart/remove-item/{shoppingCartItemId}', name: 'shopping_cart_remove_item', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(string $shoppingCartItemId): Response
    {
        $responseDto = $this->removeItemFromShoppingCartService->handle(
            RemoveItemFromShoppingCartInputDto::create(
                $shoppingCartItemId,
            ),
        );

        if ($responseDto === null) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse($responseDto, Response::HTTP_CREATED);
    }
}
