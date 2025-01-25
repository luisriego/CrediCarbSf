<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Adapter\Framework\Http\API\Filter\ProjectFilter;
use App\Adapter\Framework\Http\API\Response\PaginatedResponse;
use App\Domain\Model\Project;

interface ProjectRepositoryInterface
{
    public function add(Project $project, bool $flush): void;

    public function save(Project $project, bool $flush): void;

    public function remove(Project $project, bool $flush): void;

    public function findOneByIdOrFail(string $id): Project;

    public function exists(Project $project): bool;

    public function existsWithSimilarWords(Project $project): bool;

    public function findByStatus(string $status): array;

    public function findByType(string $type): array;

    public function search(ProjectFilter $filter): PaginatedResponse;
}
