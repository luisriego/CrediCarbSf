<?php

declare(strict_types=1);

namespace App\Adapter\Database\ORM\Doctrine\Repository;

use App\Domain\Model\CertificationTypeEntity;
use App\Domain\Repository\CertificationTypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

final class DoctrineCertificationTypeRepository extends ServiceEntityRepository implements CertificationTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CertificationTypeEntity::class);
    }

    public function add(CertificationTypeEntity $certificationType, bool $flush = false): void
    {
        $this->getEntityManager()->persist($certificationType);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(CertificationTypeEntity $certificationType, bool $flush = false): void
    {
        $this->getEntityManager()->persist($certificationType);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CertificationTypeEntity $certificationType, bool $flush = false): void
    {
        $this->getEntityManager()->remove($certificationType);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function findOneByIdOrFail(string $id): CertificationTypeEntity
    {
        $certificationType = $this->find($id);

        if (null === $certificationType) {
            throw new RuntimeException('CertificationType not found');
        }

        return $certificationType;
    }
}
