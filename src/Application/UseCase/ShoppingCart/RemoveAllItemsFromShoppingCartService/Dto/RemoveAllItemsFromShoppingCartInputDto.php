<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\RemoveAllItemsFromShoppingCartService\Dto;

use App\Domain\Validation\Traits\AssertNotNullTrait;
use App\Domain\Validation\Traits\AssertValidUidTrait;

class RemoveAllItemsFromShoppingCartInputDto
{
    use AssertNotNullTrait;
    use AssertValidUidTrait;

    private const ARGS = [
        'shoppingCartId',
    ];

    public function __construct(
        public string $shoppingCartId,
    ) {
        $this->assertNotNull(self::ARGS, [
            $this->shoppingCartId,
        ]);

        $this->assertValidUid($this->shoppingCartId);
    }

    public static function create(
        string $shoppingCartId,
    ): self {
        return new static($shoppingCartId);
    }
}
