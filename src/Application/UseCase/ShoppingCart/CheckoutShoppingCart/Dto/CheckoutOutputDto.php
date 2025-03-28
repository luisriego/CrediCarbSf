<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\CheckoutShoppingCart\Dto;

use App\Domain\Model\Discount;

use function array_map;

final readonly class CheckoutOutputDto
{
    public function __construct(
        private string $id,
        private string $ownerId,
        private string $ownerName,
        private array $items,
        private float $total,
        private ?string $discountCode = null,
        private ?float $discountedTotal = null,
    ) {}

    public static function fromEntity($shoppingCart, ?Discount $discount = null): self
    {
        $owner = $shoppingCart->getOwner();
        $ownerName = '';

        $total = (float) $shoppingCart->getTotal();
        $discountedTotal = $total;

        if ($discount !== null) {
            $discountedTotal = $discount->applyToAmount((int) $total);
        }

        return new self(
            $shoppingCart->getId(),
            $owner ? $owner->getId() : '',
            $ownerName,
            array_map(
                fn ($item) => [
                    'id' => $item->getProject()->getId(),
                    'name' => $item->getProject()->getName(),
                    'quantity' => $item->getQuantity(),
                    'price' => $item->getProject()->getPrice(),
                    'total' => $item->getTotalPrice(),
                ],
                $shoppingCart->getItems()->toArray(),
            ),
            $total,
            $discount?->code(),
            $discountedTotal,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ownerId' => $this->ownerId,
            'ownerName' => $this->ownerName,
            'items' => $this->items,
            'total' => $this->total,
            'discountCode' => $this->discountCode,
            'discountedTotal' => $this->discountedTotal,
        ];
    }

    public function id(): string
    {
        return $this->id;
    }

    public function ownerId(): string
    {
        return $this->ownerId;
    }

    public function ownerName(): string
    {
        return $this->ownerName;
    }

    public function items(): array
    {
        return $this->items;
    }

    public function total(): float
    {
        return $this->total;
    }
}
