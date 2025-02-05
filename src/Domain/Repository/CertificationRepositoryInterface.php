<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Certification;

interface CertificationRepositoryInterface
{
    public function add(Certification $authority, bool $flush): void;

    public function save(Certification $authority, bool $flush): void;

    public function remove(Certification $authority, bool $flush): void;

    public function findAll(): array;

    public function findOneByIdOrFail(string $id): Certification;
}