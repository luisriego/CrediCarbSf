<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\UpdateItemQuantityInShoppingCart;

use App\Application\UseCase\ShoppingCart\UpdateItemQuantityInShoppingCart\Dto\UpdateItemQuantityInShoppingCartInputDto;
use App\Application\UseCase\ShoppingCart\UpdateItemQuantityInShoppingCart\Dto\UpdateItemQuantityInShoppingCartOutputDto;
use App\Domain\Repository\ShoppingCartItemRepositoryInterface;
use App\Domain\Repository\ShoppingCartRepositoryInterface;
use DomainException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class UpdateItemQuantityInShoppingCartService
{
    public function __construct(
        private ShoppingCartRepositoryInterface $shoppingCartRepository,
        private ShoppingCartItemRepositoryInterface $shoppingCartItemRepository,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    public function handle(UpdateItemQuantityInShoppingCartInputDto $inputDto): UpdateItemQuantityInShoppingCartOutputDto
    {
        $shoppingCart = $this->shoppingCartRepository->findOneByIdOrFail($inputDto->shoppingCartId);
        $shoppingCartItem = $this->shoppingCartItemRepository->findOneByIdOrFail($inputDto->itemId);

        if (!$this->authorizationChecker->isGranted('modify', $shoppingCart)) {
            throw new DomainException('You are not the owner of this shopping cart.');
        }

        $shoppingCartItem->incrementQuantityIn($inputDto->quantity);

        $this->shoppingCartItemRepository->save($shoppingCartItem, true);

        return UpdateItemQuantityInShoppingCartOutputDto::create($shoppingCart->getId(), $shoppingCartItem->toArray());
    }
}
