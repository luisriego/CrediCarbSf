<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\User;

interface UserContextInterface
{
    public function getCurrentUser(): ?User;

    public function isAuthenticated(): bool;
}
