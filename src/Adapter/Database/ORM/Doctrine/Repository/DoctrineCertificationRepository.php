<?php

declare(strict_types=1);

namespace App\Adapter\Database\ORM\Doctrine\Repository;

use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\Certification;
use App\Domain\Repository\CertificationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineCertificationRepository extends ServiceEntityRepository implements CertificationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Certification::class);
    }

    public function add(Certification $authority, bool $flush): void
    {
        $this->getEntityManager()->persist($authority);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(Certification $authority, bool $flush): void
    {
        $this->getEntityManager()->persist($authority);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Certification $authority, bool $flush): void
    {
        $this->getEntityManager()->remove($authority);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByIdOrFail(string $id): Certification
    {
        if (null === $authority = $this->findOneBy(['id' => $id])) {
            throw ResourceNotFoundException::createFromClassAndId(Certification::class, $id);
        }

        return $authority;
    }
}
