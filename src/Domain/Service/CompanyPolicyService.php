<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Common\UserRole;
use App\Domain\Model\User;
use App\Domain\Policy\CompanyPolicyInterface;

readonly class CompanyPolicyService implements CompanyPolicyInterface
{
    public function canAddUser(User $user, string $companyId): bool
    {
        if ($user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        if ($user->belongsToCompany($companyId) && $user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        return false;
    }

    public function canCreate(User $user): bool
    {
        return true;
    }

    public function canDelete(User $user, string $companyId): bool
    {
        if ($user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        if ($user->belongsToCompany($companyId) && $user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        return false;
    }

    public function canUpdate(User $user, string $companyId): bool
    {
        if ($user->hasRole(UserRole::OPERATOR)) {
            return true;
        }

        if ($user->belongsToCompany($companyId)) {
            return true;
        }

        return false;
    }

    public function canView(User $user, string $companyId): bool
    {
        return true;
    }
}
