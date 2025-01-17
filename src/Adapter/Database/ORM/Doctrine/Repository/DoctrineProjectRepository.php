<?php

declare(strict_types=1);

namespace App\Adapter\Database\ORM\Doctrine\Repository;

use App\Adapter\Framework\Http\API\Filter\ProjectFilter;
use App\Adapter\Framework\Http\API\Response\PaginatedResponse;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\Project;
use App\Domain\Repository\ProjectRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

use function sprintf;

class DoctrineProjectRepository extends ServiceEntityRepository implements ProjectRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function add(Project $project, bool $flush): void
    {
        $this->getEntityManager()->persist($project);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(Project $project, bool $flush): void
    {
        $this->getEntityManager()->persist($project);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $project, bool $flush): void
    {
        $this->getEntityManager()->remove($project);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByIdOrFail(string $id): Project
    {
        if (null === $project = $this->findOneBy(['id' => $id])) {
            throw ResourceNotFoundException::createFromClassAndId(Project::class, $id);
        }

        return $project;
    }

    /** @return array<int, Project> */
    public function findAll(): array
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
    }

    public function isDuplicate(
        string $name,
        ?string $areaHa,
        ?string $quantity,
        ?string $price,
        ?string $projectType
    ): bool {
        $parameters = new ArrayCollection([
            'name' => $name,
            'areaHa' => $areaHa,
            'quantity' => $quantity,
            'price' => $price,
            'projectType' => $projectType,
        ]);
    
        $result = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.name = :name')
            ->andWhere('p.areaHa = :areaHa')
            ->andWhere('p.quantity = :quantity')
            ->andWhere('p.price = :price')
            ->andWhere('p.projectType = :projectType')
            ->setParameters($parameters)
            ->getQuery()
            ->getSingleScalarResult();
    
        return $result > 0;
    }

    /**
     * @throws Exception
     */
    public function search(ProjectFilter $filter): PaginatedResponse
    {
        $page = $filter->page;
        $limit = $filter->limit;
        $sort = $filter->sort;
        $order = $filter->order;
        $areaHa = $filter->areaHa;
        $quantity = $filter->quantity;

        $qb = $this->createQueryBuilder('p');
        $qb->orderBy(sprintf('p.%s', $sort), $order);

        if (null !== $areaHa) {
            $qb
                ->andWhere('p.areaHa = :areaHa')
                ->setParameter(':areaHa', $areaHa);
        }

        if (null !== $quantity) {
            $qb
                ->andWhere('p.quantity = :quantity')
                ->setParameter(':quantity', $quantity);
        }

        $paginator = new Paginator($qb->getQuery());
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return PaginatedResponse::create($paginator->getIterator(), $paginator->count(), $page, $limit);
    }
}
