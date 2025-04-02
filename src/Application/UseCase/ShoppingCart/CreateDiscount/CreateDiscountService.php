<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\CreateDiscount;

use App\Application\UseCase\ShoppingCart\CreateDiscount\Dto\CreateDiscountInputDto;
use App\Application\UseCase\User\UserFinder\UserFinder;
use App\Domain\Exception\ShoppingCart\DiscountAlreadyExistsException;
use App\Domain\Factory\DiscountFactory;
use App\Domain\Repository\DiscountRepositoryInterface;
use Random\RandomException;

final readonly class CreateDiscountService
{
    public function __construct(
        private DiscountRepositoryInterface $discountRepository,
        private DiscountFactory $discountFactory,
        private UserFinder $userFinder,
    ) {}

    /**
     * @throws RandomException|DiscountAlreadyExistsException
     */
    public function handle(CreateDiscountInputDto $inputDto): string
    {
        $user = $this->userFinder->getCurrentUser();

        $discount = $this->discountFactory->create(
            $user,
            $inputDto->amount,
            $inputDto->expiresAt,
            $inputDto->isPercentage,
            $inputDto->projectId,
        );

        // use the TDA principle, may be avoidRepeated method
        if ($this->discountRepository->exists($discount)) {
            throw DiscountAlreadyExistsException::createRepeated();
        }

        $this->discountRepository->save($discount, true);

        return $discount->code();
    }
}
