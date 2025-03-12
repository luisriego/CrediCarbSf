<?php

declare(strict_types=1);

namespace App\Adapter\Database\ORM\Doctrine\Repository;

use App\Adapter\Framework\Http\API\Filter\CompanyFilter;
use App\Adapter\Framework\Http\API\Response\PaginatedResponse;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use InvalidArgumentException;

use function sprintf;

class DoctrineCompanyRepository extends ServiceEntityRepository implements CompanyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function add(Company $company, bool $flush): void
    {
        if (empty($company->fantasyName())) {
            throw new InvalidArgumentException('The company name cannot be empty.');
        }

        if (empty($company->taxPayer())) {
            throw new InvalidArgumentException('The company taxpayer cannot be empty.');
        }

        $this->getEntityManager()->persist($company);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(Company $company, bool $flush): void
    {
        $this->getEntityManager()->persist($company);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Company $company, bool $flush): void
    {
        $this->getEntityManager()->remove($company);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByIdOrFail(string $id): Company
    {
        if (null === $company = $this->findOneBy(['id' => $id])) {
            throw ResourceNotFoundException::createFromClassAndId(Company::class, $id);
        }

        return $company;
    }

    public function findOneByFantasyNameOrFail(string $fantasyName): Company
    {
        if (null === $company = $this->findOneBy(['fantasyName' => $fantasyName])) {
            throw ResourceNotFoundException::createFromClassAndName(Company::class, $fantasyName);
        }

        return $company;
    }

    public function findByFantasyNameOrFail(string $fantasyName): array
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.fantasyName LIKE :fantasyName')
            ->setParameter('fantasyName', '%' . $fantasyName . '%');

        $company = $qb->getQuery()->getResult();

        if (empty($company)) {
            throw ResourceNotFoundException::createFromClassAndName(Company::class, $fantasyName);
        }

        return $company;
    }

    public function findOneByTaxpayerOrFail(string $taxpayer): Company
    {
        if (null === $company = $this->findOneBy(['taxpayer' => $taxpayer])) {
            throw ResourceNotFoundException::createFromClassAndProperty(Company::class, 'Taxpayer', $taxpayer);
        }

        return $company;
    }

    public function existByTaxpayer(string $taxpayer): ?Company
    {
        return $this->findOneBy(['taxpayer' => $taxpayer]);
    }

    public function existById(string $id): bool
    {
        return (bool) $this->findOneBy(['id' => $id]);
    }

    /**
     * @throws Exception
     */
    public function search(CompanyFilter $filter): PaginatedResponse
    {
        $page = $filter->page;
        $limit = $filter->limit;
        $sort = $filter->sort;
        $order = $filter->order;
        $name = $filter->name;
        $taxpayer = $filter->taxpayer;
        $fantasyName = $filter->fantasyName;

        $qb = $this->createQueryBuilder('c');
        $qb->orderBy(sprintf('c.%s', $sort), $order);

        if (null !== $name) {
            $qb
                ->andWhere('c.name LIKE :name')
                ->setParameter(':name', $name . '%');
        }

        if (null !== $taxpayer) {
            $qb
                ->andWhere('c.taxpayer = :taxpayer')
                ->setParameter(':taxpayer', $taxpayer);
        }

        if (null !== $fantasyName) {
            $qb
                ->andWhere('c.fantasyName LIKE :fantasyName')
                ->setParameter(':fantasyName', $fantasyName . '%');
        }

        $paginator = new Paginator($qb->getQuery());
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return PaginatedResponse::create($paginator->getIterator()->getArrayCopy(), $paginator->count(), $page, $limit);
    }
}
