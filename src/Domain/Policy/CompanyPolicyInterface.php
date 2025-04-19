<?php

declare(strict_types=1);

namespace App\Domain\Policy;

use App\Domain\Model\User;

interface CompanyPolicyInterface
{
    public function canAddUser(User $user, string $companyId): bool;

    public function canCreate(User $user): bool;

    public function canDelete(User $user, string $companyId): bool;

    public function canUpdate(User $userId, string $companyId): bool;

    public function canView(User $user, string $companyId): bool;
}
