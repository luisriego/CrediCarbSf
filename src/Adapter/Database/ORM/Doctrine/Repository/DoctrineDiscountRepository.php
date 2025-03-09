<?php

declare(strict_types=1);

namespace App\Adapter\Database\ORM\Doctrine\Repository;

use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\Discount;
use App\Domain\Repository\DiscountRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineDiscountRepository extends ServiceEntityRepository implements DiscountRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discount::class);
    }

    public function add(Discount $discount, bool $flush): void
    {
        $this->getEntityManager()->persist($discount);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(Discount $discount, bool $flush): void
    {
        $this->getEntityManager()->persist($discount);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Discount $discount, bool $flush): void
    {
        $this->getEntityManager()->remove($discount);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByIdOrFail(string $id): Discount
    {
        if (null === $discount = $this->findOneBy(['id' => $id])) {
            throw ResourceNotFoundException::createFromClassAndId(Discount::class, $id);
        }

        return $discount;
    }

    public function findOneByCodeOrFail(string $code): Discount
    {
        if (null === $discount = $this->findOneBy(['code' => $code])) {
            throw ResourceNotFoundException::createFromClassAndCode(Discount::class, $code);
        }

        return $discount;
    }

    public function exists(Discount $discount): bool
    {
        $qb = $this->createQueryBuilder('d')
            ->select('1')
            ->where('d.createdBy = :createdBy')
            ->andWhere('d.amount = :amount')
            ->andWhere('d.expiresAt = :expiresAt')
            ->setParameter('amount', $discount->amount())
            ->setParameter('createdBy', $discount->createdBy()->getId())
            ->setParameter('expiresAt', $discount->expiresAt());

        return (bool) $qb->getQuery()->getOneOrNullResult();
    }
}
