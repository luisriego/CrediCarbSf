<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\CertificationAuthority;

interface CertificationAuthorityRepositoryInterface
{
    public function add(CertificationAuthority $authority, bool $flush): void;

    public function save(CertificationAuthority $authority, bool $flush): void;

    public function remove(CertificationAuthority $authority, bool $flush): void;

    public function findAll(): array;

    public function findOneByIdOrFail(string $id): CertificationAuthority;

    public function findOneByNameOrFail(string $fantasyName): CertificationAuthority;

    public function findOneByWebsiteOrFail(string $website): CertificationAuthority;

    public function exists(CertificationAuthority $authority): bool;

    public function findByCountry(string $country): array;

    public function findByCertification(string $certification): array;
}
