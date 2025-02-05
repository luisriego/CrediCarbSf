<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Common\ShoppingCartStatus;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use

#[ORM\Entity(repositoryClass: ShoppingCartRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
class ShoppingCart
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;

    #[ORM\OneToMany(targetEntity: ShoppingCartItem::class, mappedBy: 'shoppingCart', cascade: ['persist', 'remove'])]
    private Company $Owner;

    #[ORM\OneToMany(targetEntity: ShoppingCartItem::class, mappedBy: 'shoppingCart', cascade: ['persist', 'remove'])]
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
        $this->Owner = $Owner;
        $this->isActive = true;
        $this->initializeCreatedOn();
        $this->items = new ArrayCollection();
        $this->status = ShoppingCartStatus::PENDING();
    }
}