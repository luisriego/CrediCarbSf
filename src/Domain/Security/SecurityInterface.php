<?php

declare(strict_types=1);

namespace App\Domain\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface SecurityInterface
{
    public function security(Security $security): string;
    public function isGranted(AuthorizationCheckerInterface $authChecker, string $attribute, $subject = null): bool;
    public function getUser(TokenStorageInterface $tokenStorage): ?UserInterface;
}
