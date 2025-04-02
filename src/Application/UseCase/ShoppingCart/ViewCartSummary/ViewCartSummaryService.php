<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\ViewCartSummary;

use App\Application\UseCase\ShoppingCart\ViewCartSummary\Dto\ViewCartSummaryOutputDto;
use App\Domain\Model\User;
use App\Domain\Repository\ShoppingCartRepositoryInterface;

final readonly class ViewCartSummaryService
{
    public function __construct(
        private ShoppingCartRepositoryInterface $shoppingCartRepository,
    ) {}

    public function handle(User $actualUser): ViewCartSummaryOutputDto
    {
        $shoppingCart = $this->shoppingCartRepository->findOneByOwnerIdOrFail($actualUser->getCompany()->getId());

        //        if (null === $actualUser->getCompany()) {
        //            return new ViewCartSummaryOutputDto([], '0.00');
        //        }

        return new ViewCartSummaryOutputDto(
            $shoppingCart->toArray(),
            $shoppingCart->getTotal(),
        );
    }
}
