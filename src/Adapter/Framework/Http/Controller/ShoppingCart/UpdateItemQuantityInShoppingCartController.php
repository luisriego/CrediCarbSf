<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\ShoppingCart;

use App\Adapter\Framework\Http\Dto\ShoppingCart\UpdateItemQuantityInShoppingCartRequestDto;
use App\Application\UseCase\ShoppingCart\UpdateItemQuantityInShoppingCart\Dto\UpdateItemQuantityInShoppingCartInputDto;
use App\Application\UseCase\ShoppingCart\UpdateItemQuantityInShoppingCart\UpdateItemQuantityInShoppingCartService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

readonly class UpdateItemQuantityInShoppingCartController
{
    public function __construct(
        private UpdateItemQuantityInShoppingCartService $updateItemQuantityInShoppingCartService,
    ) {}

    #[Route('/api/shopping-cart/{shoppingCartId}/quantity', name: 'shopping_cart_update_item_quantity', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(UpdateItemQuantityInShoppingCartRequestDto $requestDto): JsonResponse
    {
        $responseDto = $this->updateItemQuantityInShoppingCartService->handle(
            UpdateItemQuantityInShoppingCartInputDto::create(
                $requestDto->shoppingCartId,
                $requestDto->itemId,
                $requestDto->quantity,
            ),
        );

        return new JsonResponse($responseDto, Response::HTTP_OK);
    }
}
