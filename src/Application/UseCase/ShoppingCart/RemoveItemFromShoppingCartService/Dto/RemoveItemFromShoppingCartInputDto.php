<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\RemoveItemFromShoppingCartService\Dto;

use App\Domain\Validation\Traits\AssertNotNullTrait;
use App\Domain\Validation\Traits\AssertValidUidTrait;

class RemoveItemFromShoppingCartInputDto
{
    use AssertNotNullTrait;
    use AssertValidUidTrait;

    private const ARGS = [
        'shoppingCartItemId',
    ];

    public function __construct(
        public string $shoppingCartItemId,
    ) {
        $this->assertNotNull(self::ARGS, [
            $this->shoppingCartItemId,
        ]);

        $this->assertValidUid($this->shoppingCartItemId);
    }

    public static function create(
        string $shoppingCartItemId,
    ): self {
        return new static($shoppingCartItemId);
    }
}
