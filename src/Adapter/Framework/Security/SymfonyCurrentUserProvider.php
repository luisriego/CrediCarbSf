<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Security;

use App\Domain\Model\User;
use App\Domain\Security\CurrentUserProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class SymfonyCurrentUserProvider implements CurrentUserProviderInterface
{
    public function __construct(
        private Security $security,
    ) {}

    public function getCurrentUser(): ?object
    {
        return $this->security->getUser();
    }

    public function getCurrentUserId(): ?string
    {
        $user = $this->getCurrentUser();

        return $user instanceof User ? $user->getId() : null;
    }

    public function hasRole(string $role): bool
    {
        return $this->security->isGranted($role);
    }
}
