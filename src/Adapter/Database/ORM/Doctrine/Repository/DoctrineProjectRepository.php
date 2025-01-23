<?php

declare(strict_types=1);

namespace App\Adapter\Database\ORM\Doctrine\Repository;

use App\Adapter\Framework\Http\API\Filter\ProjectFilter;
use App\Adapter\Framework\Http\API\Response\PaginatedResponse;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\Project;
use App\Domain\Repository\ProjectRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

use function explode;
use function mb_strtolower;
use function sort;
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

    public function exists(Project $project): bool
    {
        $qb = $this->createQueryBuilder('p')
            ->select('1')
            ->where('p.name = :name')
            ->andWhere('p.areaHa = :areaHa')
            ->andWhere('p.quantity = :quantity')
            ->andWhere('p.price = :price')
            ->andWhere('p.projectType = :projectType')
            ->setParameter('name', $project->getName())
            ->setParameter('areaHa', $project->getAreaHa())
            ->setParameter('quantity', $project->getQuantity())
            ->setParameter('price', $project->getPrice())
            ->setParameter('projectType', $project->getProjectType())
        ;

        return (bool) $qb->getQuery()->getOneOrNullResult();
    }

    public function existsWithSimilarWords(Project $project): bool
    {
        $projects = $this->createQueryBuilder('p')
            ->select('p')
            ->getQuery()
            ->getResult();

        $projectWords = $this->tokenize($project->getName());

        foreach ($projects as $existingProject) {
            $existingProjectWords = $this->tokenize($existingProject->getName());

            if ($projectWords === $existingProjectWords) {
                return true;
            }
        }

        return false;
    }

    public function findByStatus(string $status): array
    {
        return $this->findBy(['status' => $status]);
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

    private function tokenize(string $name): array
    {
        $words = explode(' ', mb_strtolower($name));
        sort($words);

        return $words;
    }
}
