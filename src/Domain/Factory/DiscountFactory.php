<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Model\Discount;
use App\Domain\Model\Project;
use App\Domain\Model\User;
use Random\RandomException;

class DiscountFactory
{
    /**
     * @throws RandomException
     */
    public function create(User $user, int $amount, ?string $expiresAt, ?bool $isPercentage = true, ?Project $project = null): Discount
    {
        if ($project) {
            return Discount::createWithProjectToApply(
                $user,
                $amount,
                $expiresAt,
                $project,
            );
        }

        if (!$isPercentage) {
            return Discount::createWithAmountAndExpirationDateNotPercentage(
                $user,
                $amount,
                $expiresAt,
                false,
            );
        }

        return Discount::createWithAmountAndExpirationDate(
            $user,
            $amount,
            $expiresAt,
        );
    }
}
