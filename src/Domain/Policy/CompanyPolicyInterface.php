<?php

declare(strict_types=1);

namespace App\Domain\Policy;

interface CompanyPolicyInterface
{
    public function canAddUserOrFail(string $companyId): void;

    public function canCreateOrFail(): void;

    public function canDeleteOrFail(string $companyId): void;

    public function canUpdateOrFail(string $companyId): void;

    public function canViewOrFail(): void;
}
