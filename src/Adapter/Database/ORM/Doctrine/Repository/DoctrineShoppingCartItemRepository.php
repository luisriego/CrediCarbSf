<?php

declare(strict_types=1);

namespace App\Adapter\Database\ORM\Doctrine\Repository;

use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\ShoppingCartItem;
use App\Domain\Repository\ShoppingCartItemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineShoppingCartItemRepository extends ServiceEntityRepository implements ShoppingCartItemRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShoppingCartItem::class);
    }

    public function add(ShoppingCartItem $shoppingCartItem, bool $flush): void
    {
        $this->getEntityManager()->persist($shoppingCartItem);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(ShoppingCartItem $shoppingCartItem, bool $flush): void
    {
        $this->getEntityManager()->persist($shoppingCartItem);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ShoppingCartItem $shoppingCartItem, bool $flush): void
    {
        $this->getEntityManager()->remove($shoppingCartItem);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByIdOrFail(string $id): ShoppingCartItem
    {
        if (null === $shoppingCartItem = $this->findOneBy(['id' => $id])) {
            throw ResourceNotFoundException::createFromClassAndId(ShoppingCartItem::class, $id);
        }

        return $shoppingCartItem;
    }

    public function findByShoppingCartId(string $shoppingCartId): array
    {
        return $this->findBy(['shoppingCart' => $shoppingCartId]);
    }
}