<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Security;

use App\Domain\Model\User;
use App\Domain\Service\UserContextInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class SecurityUserContext implements UserContextInterface
{
    public function __construct(
        private Security $security,
    ) {}

    public function getCurrentUser(): ?User
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    public function isAuthenticated(): bool
    {
        return $this->security->getUser() instanceof UserInterface;
    }
}
