<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Model\Company;
use App\Domain\Model\User;
use App\Domain\Security\PasswordHasherInterface;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;

use function is_bool;

class UserFactory
{
    public function createUser(
        string $name,
        string $email,
        string $password,
        ?Company $company = null,
        array $roles = [],
        bool $isActive = false,
    ): User {
        $user = User::create(
            $name,
            new Email($email),
            new Password($password),
        );

        if ($company) {
            $user->setCompany($company);
        }

        $user->setRoles($roles);

        if ($isActive) {
            $user->setIsActive(is_bool($isActive));
        }


        return $user;
    }
}
