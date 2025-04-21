<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Common\UserRole;
use App\Domain\Exception\AccessDeniedException;
use App\Domain\Model\User;
use App\Domain\Policy\CompanyPolicyInterface;
use App\Domain\Security\CurrentUserProviderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class CompanyPolicyService implements CompanyPolicyInterface
{
    public function __construct(
        private readonly CurrentUserProviderInterface $currentUserProvider,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    public function canAddUserOrFail(string $companyId): void
    {
        $user = $this->currentUserProvider->getCurrentUser();

        if ($this->authorizationChecker->isGranted(UserRole::OPERATOR->value)) {
            return;
        }

        if ($user->belongsToCompany($companyId)
            && $this->authorizationChecker->isGranted(UserRole::OPERATOR->value)) {
            return;
        }

        throw AccessDeniedException::UnauthorizedUser();
    }

    public function canCreateOrFail(): void
    {
        if ($this->authorizationChecker->isGranted(UserRole::OPERATOR->value)) {
            return;
        }

        throw AccessDeniedException::UnauthorizedUser();
    }

    public function canDeleteOrFail(string $companyId): void
    {
        $user = $this->currentUserProvider->getCurrentUser();

        if ($this->authorizationChecker->isGranted(UserRole::OPERATOR->value)) {
            return;
        }

        if ($user->belongsToCompany($companyId)
            && $this->authorizationChecker->isGranted(UserRole::OPERATOR->value)) {
            return;
        }

        throw AccessDeniedException::UnauthorizedUser();
    }

    public function canUpdateOrFail(string $companyId): void
    {
        $user = $this->currentUserProvider->getCurrentUser();

        if ($this->authorizationChecker->isGranted(UserRole::OPERATOR->value)) {
            return;
        }

        if ($user->belongsToCompany($companyId)
            && $this->authorizationChecker->isGranted(UserRole::OPERATOR->value)) {
            return;
        }

        throw AccessDeniedException::UnauthorizedUser();
    }

    public function canViewOrFail(): void
    {
        if ($this->authorizationChecker->isGranted(UserRole::USER->value)) {
            return;
        }

        throw AccessDeniedException::UnauthorizedUser();
    }
}
