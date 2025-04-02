<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\ApproveDiscount;

use App\Application\UseCase\ShoppingCart\ApproveDiscount\Dto\ApproveDiscountInputDto;
use App\Domain\Exception\Security\UnauthorizedDiscountApprovalException;
use App\Domain\Repository\DiscountRepositoryInterface;
use App\Domain\Service\UserContextInterface;

final readonly class ApproveDiscountService
{
    public function __construct(
        private DiscountRepositoryInterface $discountRepository,
        private UserContextInterface $userContext,
    ) {}

    /**
     * @throws UnauthorizedDiscountApprovalException
     */
    public function handle(ApproveDiscountInputDto $inputDto): void
    {
        $discount = $this->discountRepository->findOneByIdOrFail($inputDto->discountId);

        $discount->approve($this->userContext->getCurrentUser());
        $this->discountRepository->save($discount, true);
    }
}
