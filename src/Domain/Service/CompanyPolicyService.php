<?php

namespace App\Domain\Service;

use App\Domain\Common\UserRole;
use App\Domain\Model\User;
use App\Domain\Policy\CompanyPolicyInterface;

readonly class CompanyPolicyService implements CompanyPolicyInterface
{
    public function canCreate(string $userId): bool
    {
        return true;
    }

    public function canDelete(string $userId, string $companyId): bool
    {
        return false;
    }

    public function canUpdate(User $user, string $companyId): bool
    {
        if ($user->belongsToCompany($companyId)) {
            return true;
        }

        if ($user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        return false;
    }

    public function canView(string $userId, string $companyId): bool
    {
        return true;
    }
}