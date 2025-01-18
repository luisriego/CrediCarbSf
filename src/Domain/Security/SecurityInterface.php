<?php

declare(strict_types=1);

namespace App\Domain\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

interface SecurityInterface
{
    public function security(Security $security): string;

    public function getUser(Security $security): ?UserInterface;
}
