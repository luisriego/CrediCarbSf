<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\CreateDiscount\Dto;

use App\Domain\Validation\Traits\AssertNotNullTrait;
use App\Domain\Validation\Traits\AssertValidDateTimeTrait;
use App\Domain\Validation\Traits\AssertValidQuantityTrait;
use App\Domain\Validation\Traits\AssertValidUidTrait;

class CreateDiscountInputDto
{
    use AssertNotNullTrait;
    use AssertValidUidTrait;
    use AssertValidDateTimeTrait;
    use AssertValidQuantityTrait;

    private const ARGS = [
        'creatorId',
        'amount',
    ];

    public function __construct(
        public string $creatorId,
        public int $amount,
        public ?string $expiresAt,
        public ?bool $isPercentage = true,
        public ?string $projectId = null,
    ) {
        $this->assertNotNull(self::ARGS, [
            $this->creatorId,
            $this->amount,
        ]);

        $this->assertValidUid($this->creatorId);
        $this->assertValidQuantity($this->amount);
    }

    public static function create(
        string $creatorId,
        int $amount,
        ?string $expiresAt,
        ?bool $isPercentage = true,
        ?string $projectId = null,
    ): self {
        return new static($creatorId, $amount, $expiresAt, $isPercentage, $projectId);
    }
}
