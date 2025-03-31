<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Discount;

interface DiscountRepositoryInterface
{
    public function add(Discount $discount, bool $flush): void;

    public function save(Discount $discount, bool $flush): void;

    public function remove(Discount $discount, bool $flush): void;

    public function findOneByIdOrFail(string $id): Discount;

    public function findOneByCodeOrFail(string $code): Discount;

    public function findOneByCode(?string $code): ?Discount;

    public function exists(Discount $discount): bool;
}
