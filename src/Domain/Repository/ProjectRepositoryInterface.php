<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Adapter\Framework\Http\API\Filter\ProjectFilter;
use App\Adapter\Framework\Http\API\Response\PaginatedResponse;
use App\Domain\Exception\Project\ProjectAlreadyExistsException;
use App\Domain\Model\Project;

interface ProjectRepositoryInterface
{
    public function add(Project $project, bool $flush): void;

    public function save(Project $project, bool $flush): void;

    public function remove(Project $project, bool $flush): void;

    public function findOneByIdOrFail(string $id): Project;

    /** @return array<int, Project> */
    public function findAll(): array;

    /** @throws ProjectAlreadyExistsException */
    public function isDuplicate(
        string $name,
        ?string $areaHa,
        ?string $quantity,
        ?string $price,
        ?string $projectType
    ): bool;

    public function search(ProjectFilter $filter): PaginatedResponse;
}
