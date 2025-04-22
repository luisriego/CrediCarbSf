<?php

declare(strict_types=1);

namespace App\Domain\Security;

interface CurrentUserProviderInterface
{
    public function getCurrentUser(): ?object;

    public function getCurrentUserId(): ?string;

    public function hasRole(string $role): bool;
}
