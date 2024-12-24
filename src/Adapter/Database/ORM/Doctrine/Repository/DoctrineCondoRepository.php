<?php

declare(strict_types=1);

namespace App\Adapter\Database\ORM\Doctrine\Repository;

use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\Condo;
use App\Domain\Repository\CondoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Condo>
 *
 * @method Condo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Condo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Condo[]    findAll()
 * @method Condo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrineCondoRepository extends ServiceEntityRepository implements CondoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Condo::class);
    }

    public function add(Condo $condo, bool $flush): void
    {
        $this->getEntityManager()->persist($condo);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(Condo $condo, bool $flush): void
    {
        $this->getEntityManager()->persist($condo);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Condo $condo, bool $flush): void
    {
        $this->getEntityManager()->remove($condo);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByIdOrFail(string $id): Condo
    {
        if (null === $condo = $this->find($id)) {
            throw ResourceNotFoundException::createFromClassAndId(Condo::class, $id);
        }

        return $condo;
    }

    public function findOneByTaxpayer(string $taxpayer): ?Condo
    {
        return $this->findOneBy(['taxpayer' => $taxpayer]);
    }
}
