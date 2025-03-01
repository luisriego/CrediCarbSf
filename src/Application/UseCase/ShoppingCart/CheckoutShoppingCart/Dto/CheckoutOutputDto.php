<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\CheckoutShoppingCart\Dto;

final readonly class CheckoutOutputDto
{
    public function __construct(
        private string $id,
        private string $ownerId,
        private string $ownerName,
        private array  $items,
        private float  $total
    ) {
    }

    public static function fromEntity($shoppingCart): self
    {
        $owner = $shoppingCart->getOwner();
        $ownerName = '';

        return new self(
            $shoppingCart->getId(),
            $owner ? $owner->getId() : '',
            $ownerName,
            array_map(
                fn($item) => [
                    'id' => $item->getProject()->getId(),
                    'name' => $item->getProject()->getName(),
                    'quantity' => $item->getQuantity(),
                    'price' => $item->getProject()->getPrice(),
                ],
                $shoppingCart->getItems()->toArray()
            ),
            (float) $shoppingCart->getTotal()
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
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotal(): float
    {
        return $this->total;
    }
}