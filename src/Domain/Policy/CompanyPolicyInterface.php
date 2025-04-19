<?php

declare(strict_types=1);

namespace App\Domain\Policy;

use App\Domain\Model\User;

interface CompanyPolicyInterface
{
    public function canCreate(string $userId): bool;
    public function canDelete(string $userId, string $companyId): bool;
    public function canUpdate(User $userId, string $companyId): bool;
    public function canView(string $userId, string $companyId): bool;


}