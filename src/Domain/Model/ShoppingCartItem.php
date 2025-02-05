<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Repository\ShoppingCartItemRepositoryInterface;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingCartItemRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
class ShoppingCartItem
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $price;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'shoppingCartItems')]
    private Project $project;

    // #[ORM\ManyToOne(targetEntity: ShoppingCart::class, inversedBy: 'items')]
    // private ShoppingCart $shoppingCart;

    public function __construct(Project $project, int $quantity, string $price)
    {
        $this->initializeId();
        $this->project = $project;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->isActive = true;
        $this->initializeCreatedOn();
    }

    public static function create($project, $quantity, $price): self
    {
        return new self($project, $quantity, $price);
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    // public function getShoppingCart(): ShoppingCart
    // {
    //     return $this->shoppingCart;
    // }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    // public function setShoppingCart(ShoppingCart $shoppingCart): void{}

    public function getTotalPrice(): string
    {
        return bcmul($this->price, (string) $this->quantity, 2);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'project' => $this->getProject()->toArray(),
            'quantity' => $this->getQuantity(),
            'price' => $this->getPrice(),
            'totalPrice' => $this->getTotalPrice(),
        ];
    }
}