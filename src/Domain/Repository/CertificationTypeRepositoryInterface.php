<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\CertificationTypeEntity;

interface CertificationTypeRepositoryInterface
{
    public function add(CertificationTypeEntity $certificationType, bool $flush): void;

    public function save(CertificationTypeEntity $certificationType, bool $flush): void;

    public function remove(CertificationTypeEntity $certificationType, bool $flush): void;

    public function findAll(): array;

    public function findOneByIdOrFail(string $id): CertificationTypeEntity;
}
