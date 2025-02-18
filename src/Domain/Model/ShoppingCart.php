<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Common\ShoppingCartStatus;
use App\Domain\Repository\ShoppingCartRepositoryInterface;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use function number_format;

#[ORM\Entity(repositoryClass: ShoppingCartRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
class ShoppingCart
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'shoppingCarts')]
    private Company $owner;

    #[ORM\OneToMany(targetEntity: ShoppingCartItem::class, mappedBy: 'shoppingCart', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $total;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $tax;

    #[ORM\Column(type: 'string', enumType: ShoppingCartStatus::class)]
    private ShoppingCartStatus $status;

    public function __construct(Company $Owner)
    {
        $this->initializeId();
        $this->owner = $Owner;
        $this->isActive = true;
        $this->initializeCreatedOn();
        $this->items = new ArrayCollection();
        $this->status = ShoppingCartStatus::ACTIVE;
        $this->total = '0.00';
        $this->tax = '0.00';
    }

    public function getOwner(): Company
    {
        return $this->owner;
    }

    public function setOwner(Company $Owner): void
    {
        $this->owner = $Owner;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items): void
    {
        $this->items = $items;
    }

    public function getTotal(): string
    {
        return $this->total;
    }

    public function setTotal(string $total): void
    {
        $this->total = $total;
    }

    public function getTax(): string
    {
        return $this->tax;
    }

    public function setTax(string $tax): void
    {
        $this->tax = $tax;
    }

    public function getStatus(): ShoppingCartStatus
    {
        return $this->status;
    }

    public function setStatus(ShoppingCartStatus $status): void
    {
        $this->status = $status;
    }

    public function addItem(ShoppingCartItem $item): void
    {
        $this->items->add($item);
        $item->setShoppingCart($this);
        $this->calculateTotal();
    }

    public function removeItem(ShoppingCartItem $item): void
    {
        $this->items->removeElement($item);
        $item->setShoppingCart(null);
        $this->calculateTotal();
    }

    public function removeAllItems(): void
    {
        $this->items->clear();
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        $total = 0;
        $tax = 0;

        foreach ($this->items as $item) {
            $total += $item->getPrice() * $item->getQuantity();
            $tax += $item->getPrice() * $item->getQuantity() * 0.1;
        }
        $this->total = number_format($total, 2, '.', '');
        $this->tax = number_format($tax, 2, '.', '');
    }

    public function checkout(): void
    {
        $this->status = ShoppingCartStatus::COMPLETED;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'owner' => $this->owner->toArray(),
            'items' => $this->items->map(fn (ShoppingCartItem $item) => $item->toArray())->toArray(),
            'total' => $this->total,
            'tax' => $this->tax,
            'status' => $this->status->getValue(),
            'createdOn' => $this->createdOn,
            'updatedOn' => $this->updatedOn,
        ];
    }
}
