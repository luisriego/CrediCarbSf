<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\ApproveDiscount\Dto;

use App\Domain\Validation\Traits\AssertNotNullTrait;
use App\Domain\Validation\Traits\AssertValidUidTrait;

class ApproveDiscountInputDto
{
    use AssertNotNullTrait;
    use AssertValidUidTrait;

    private const ARGS = [
        'discountId',
    ];

    public function __construct(
        public string $discountId,
    ) {
        $this->assertNotNull(self::ARGS, [
            $this->discountId,
        ]);

        $this->assertValidUid($this->discountId);
    }

    public static function create(
        string $discountId,
    ): self {
        return new static($discountId);
    }
}
