<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\ShoppingCart;

use App\Adapter\Framework\Http\Dto\ShoppingCart\AddItemToShoppingCartRequestDto;
use App\Application\UseCase\ShoppingCart\AddItemToShoppingCartService\AddItemToShoppingCartService;
use App\Application\UseCase\ShoppingCart\AddItemToShoppingCartService\Dto\AddItemToShoppingCartInputDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class AddItemToShoppingCartController
{
    public function __construct(
        private AddItemToShoppingCartService $addItemToShoppingCartService,
    ) {}

    #[Route('/api/shopping-cart/add-item', name: 'shopping_cart_add_item', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(AddItemToShoppingCartRequestDto $requestDto): Response
    {
        $responseDto = $this->addItemToShoppingCartService->handle(
            AddItemToShoppingCartInputDto::create(
                $requestDto->ownerId,
                $requestDto->projectId,
                $requestDto->quantity,
                $requestDto->price,
            ),
        );

        return new JsonResponse($responseDto, Response::HTTP_CREATED);
    }
}
