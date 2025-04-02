<?php

declare(strict_types=1);

namespace App\Adapter\Database\ORM\Doctrine\Repository;

use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\CertificationAuthority;
use App\Domain\Repository\CertificationAuthorityRepositoryInterface;
use App\Domain\Repository\CertificationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineCertificationAuthorityRepository extends ServiceEntityRepository implements CertificationAuthorityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, private readonly CertificationRepositoryInterface $certificationRepository)
    {
        parent::__construct($registry, CertificationAuthority::class);
    }

    public function add(CertificationAuthority $authority, bool $flush): void
    {
        $this->getEntityManager()->persist($authority);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(CertificationAuthority $authority, bool $flush): void
    {
        $this->getEntityManager()->persist($authority);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CertificationAuthority $authority, bool $flush): void
    {
        $this->getEntityManager()->remove($authority);

        $certifications = $this->certificationRepository->findBy(['authority' => $authority]);

        foreach ($certifications as $certification) {
            $certification->setAuthority(null);
            $this->certificationRepository->save($certification, true);
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByIdOrFail(string $id): CertificationAuthority
    {
        if (null === $authority = $this->findOneBy(['id' => $id])) {
            throw ResourceNotFoundException::createFromClassAndId(CertificationAuthority::class, $id);
        }

        return $authority;
    }

    public function findOneByNameOrFail(string $fantasyName): CertificationAuthority
    {
        if (null === $authority = $this->findOneBy(['name' => $fantasyName])) {
            throw ResourceNotFoundException::createFromClassAndId(CertificationAuthority::class, $fantasyName);
        }

        return $authority;
    }

    public function findOneByWebsiteOrFail(string $website): CertificationAuthority
    {
        if (null === $authority = $this->findOneBy(['website' => $website])) {
            throw ResourceNotFoundException::createFromClassAndId(CertificationAuthority::class, $website);
        }

        return $authority;
    }

    public function exists(CertificationAuthority $authority): bool
    {
        $qb = $this->createQueryBuilder('c')
            ->select('1')
            ->where('c.name = :name')
            ->andWhere('c.website = :website')
            ->setParameter('name', $authority->getName())
            ->setParameter('website', $authority->getWebsite())
        ;

        return (bool) $qb->getQuery()->getOneOrNullResult();
    }

    public function findByCountry(string $country): array
    {
        return $this->findBy(['country' => $country]);
    }

    public function findByCertification(string $certification): array
    {
        return $this->findBy(['certification' => $certification]);
    }
}
