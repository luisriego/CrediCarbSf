<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\UserFinder;

use App\Domain\Model\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserFinder
{
    public function __construct(
        private readonly Security $security,
    ) {}

    public function getCurrentUser(): User
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedException('User not found');
        }

        return $user;
    }
}
