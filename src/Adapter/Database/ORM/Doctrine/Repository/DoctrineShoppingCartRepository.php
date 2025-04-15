<?php

declare(strict_types=1);

namespace App\Adapter\Database\ORM\Doctrine\Repository;

use App\Domain\Common\ShoppingCartStatus;
use App\Domain\Event\DomainEventDispatcherInterface;
use App\Domain\Event\EventSourcedEntityInterface;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\ShoppingCart;
use App\Domain\Repository\ShoppingCartRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineShoppingCartRepository extends ServiceEntityRepository implements ShoppingCartRepositoryInterface
{
    public function __construct(
        ManagerRegistry                                 $registry,
        private readonly DomainEventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($registry, ShoppingCart::class);
    }

    public function add(ShoppingCart $shoppingCart, bool $flush): void
    {
        $this->getEntityManager()->persist($shoppingCart);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(ShoppingCart $shoppingCart, bool $flush): void
    {
        $this->getEntityManager()->persist($shoppingCart);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        if ($shoppingCart instanceof EventSourcedEntityInterface) {
            $this->eventDispatcher->dispatchAll($shoppingCart->releaseEvents());
        }
    }

    public function remove(ShoppingCart $shoppingCart, bool $flush): void
    {
        $this->getEntityManager()->remove($shoppingCart);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByIdOrFail(string $id): ShoppingCart
    {
        if (null === $shoppingCart = $this->findOneBy(['id' => $id])) {
            throw ResourceNotFoundException::createFromClassAndId(ShoppingCart::class, $id);
        }

        return $shoppingCart;
    }

    public function findOwnerById(string $ownerId): ?ShoppingCart
    {
        return $this->findOneBy(['owner' => $ownerId]);
    }

    public function findActiveCartForCompanyOwnerOrFail(string $ownerId): ?ShoppingCart
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.owner = :ownerId')
            ->andWhere('c.status = :status')
            ->andWhere('c.isActive = :isActive')
            ->setParameter('companyId', $ownerId)
            ->setParameter('status', ShoppingCartStatus::ACTIVE)
            ->setParameter('isActive', true);

        $companyOwner = $qb->getQuery()->getOneOrNullResult();

        if (empty($companyOwner)) {
            throw ResourceNotFoundException::createFromClassAndId(ShoppingCart::class, $ownerId);
        }

        return $companyOwner;
    }

    public function findOneByOwnerIdOrFail(string $ownerId): ResourceNotFoundException|ShoppingCart
    {
        if (null === $shoppingCart = $this->findOneBy(['owner' => $ownerId])) {
            throw ResourceNotFoundException::createFromClassAndId(ShoppingCart::class, $ownerId);
        }

        return $shoppingCart;
    }

    public function findFirst(): ?ShoppingCart
    {
        return $this->findOneBy([]);
    }
}
