<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\AddItemToShoppingCartService;

use App\Application\UseCase\ShoppingCart\AddItemToShoppingCartService\Dto\AddItemToShoppingCartInputDto;
use App\Application\UseCase\ShoppingCart\AddItemToShoppingCartService\Dto\AddItemToShoppingCartOutputDto;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\Project;
use App\Domain\Model\ShoppingCart;
use App\Domain\Model\ShoppingCartItem;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\ProjectRepositoryInterface;
use App\Domain\Repository\ShoppingCartItemRepositoryInterface;
use App\Domain\Repository\ShoppingCartRepositoryInterface;

readonly class AddItemToShoppingCartService
{
    public function __construct(
        private ShoppingCartItemRepositoryInterface $shoppingCartItemRepository,
        private ShoppingCartRepositoryInterface $shoppingCartRepository,
        private CompanyRepositoryInterface $companyRepository,
        private ProjectRepositoryInterface $projectRepository,
    ) {}

    public function handle(AddItemToShoppingCartInputDto $inputDto): AddItemToShoppingCartOutputDto
    {
        if (null === $project = $this->projectRepository->findOneByIdOrFail($inputDto->projectId)) {
            throw ResourceNotFoundException::createFromClassAndId(Project::class, $inputDto->projectId);
        }

        if (null === $shoppingCart = $this->shoppingCartRepository->findOwnerById($inputDto->ownerId)) {
            $owner = $this->companyRepository->findOneByIdOrFail($inputDto->ownerId);
            $shoppingCart = new ShoppingCart($owner);
        }

        $shoppingCartItem = new ShoppingCartItem(
            $project,
            $inputDto->quantity,
            $inputDto->price,
        );

        $shoppingCart->addItem($shoppingCartItem);

        $this->shoppingCartItemRepository->save($shoppingCartItem, true);
        $this->shoppingCartRepository->save($shoppingCart, true);

        return AddItemToShoppingCartOutputDto::create(
            $shoppingCartItem->getShoppingCart()->getId(),
            $shoppingCart->getItems()->map(fn ($item) => $item->toArray())->toArray(),
        );
    }
}
