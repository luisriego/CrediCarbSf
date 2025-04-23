<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Model\Company;
use App\Domain\Model\User;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Password;
use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\UserName;

use function is_bool;

class UserFactory
{
    public static function createUser(
        string $id,
        string $name,
        string $email,
        string $password,
        ?Company $company = null,
        array $roles = [],
        bool $isActive = false,
    ): User {
        $user = User::create(
            UserId::fromString($id),
            UserName::fromString($name),
            Email::fromString($email),
            Password::fromString($password),
        );

        if ($company) {
            $user->assignToCompany($company);
        }

        $user->setRoles($roles);

        if ($isActive) {
            $user->setIsActive(is_bool($isActive));
        }

        return $user;
    }
}
