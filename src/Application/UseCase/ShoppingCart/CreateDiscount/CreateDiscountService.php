<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\CreateDiscount;

use App\Application\UseCase\ShoppingCart\CreateDiscount\Dto\CreateDiscountInputDto;
use App\Domain\Exception\ShoppingCart\DiscountAlreadyExistsException;
use App\Domain\Model\Discount;
use App\Domain\Repository\DiscountRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Random\RandomException;

class CreateDiscountService
{
    public function __construct(
        private readonly DiscountRepositoryInterface $discountRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @throws RandomException|DiscountAlreadyExistsException
     */
    public function handle(CreateDiscountInputDto $inputDto): string
    {
        $user = $this->userRepository->findOneByIdOrFail($inputDto->creatorId);
        $expiresAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $inputDto->expiresAt);

        $discount = Discount::createWithAmountAndExpirationDate(
            $user,
            $inputDto->amount,
            $expiresAt,
        );

        if ($this->discountRepository->exists($discount)) {
            throw DiscountAlreadyExistsException::createRepeated();
        }

        $this->discountRepository->save($discount, true);

        return $discount->getId();
    }
}
