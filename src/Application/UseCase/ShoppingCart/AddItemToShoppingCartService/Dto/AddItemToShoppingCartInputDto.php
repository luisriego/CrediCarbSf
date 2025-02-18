<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\AddItemToShoppingCartService\Dto;

use App\Domain\Validation\Traits\AssertNotNullTrait;
use App\Domain\Validation\Traits\AssertValidQuantityTrait;
use App\Domain\Validation\Traits\AssertValidUidTrait;

class AddItemToShoppingCartInputDto
{
    use AssertNotNullTrait;
    use AssertValidUidTrait;
    use AssertValidQuantityTrait;

    private const ARGS = [
        'ownerId',
        'projectId',
        'quantity',
        'price',
    ];

    public function __construct(
        public string $ownerId,
        public string $projectId,
        public int $quantity,
        public string $price,
    ) {
        $this->assertNotNull(self::ARGS, [
            $this->ownerId,
            $this->projectId,
            $this->quantity,
            $this->price,
        ]);

        $this->assertValidUid($this->ownerId);
        $this->assertValidUid($this->projectId);

        $this->assertValidQuantity($this->quantity);
        $this->assertValidPrice($this->price);
    }

    public static function create(
        string $ownerId,
        string $projectId,
        int $quantity,
        string $price,
    ): self {
        return new static($ownerId, $projectId, $quantity, $price);
    }
}
