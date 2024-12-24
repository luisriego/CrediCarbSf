<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Condo;

interface CondoRepositoryInterface
{
    public function add(Condo $condo, bool $flush): void;

    public function save(Condo $condo, bool $flush): void;

    public function remove(Condo $condo, bool $flush): void;

    public function findOneByIdOrFail(string $id): Condo;

    public function findOneByTaxpayer(string $taxpayer): ?Condo;
}
