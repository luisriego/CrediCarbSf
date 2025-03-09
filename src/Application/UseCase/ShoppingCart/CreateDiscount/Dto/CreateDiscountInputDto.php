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
        'expiresAt',
    ];

    public function __construct(
        public string $creatorId,
        public int $amount,
        public string $expiresAt,
    ) {
        $this->assertNotNull(self::ARGS, [
            $this->creatorId,
            $this->amount,
            $this->expiresAt,
        ]);

        $this->assertValidUid($this->creatorId);
        $this->assertValidQuantity($this->amount);
        $this->assertValidDateTime($this->expiresAt);
    }

    public static function create(
        string $creatorId,
        int $amount,
        string $expiresAt,
    ): self {
        return new static($creatorId, $amount, $expiresAt);
    }
}
