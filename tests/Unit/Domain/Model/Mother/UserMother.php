<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Mother;

use App\Domain\Common\UserRole;
use App\Domain\Model\Company;
use App\Domain\Model\User;

final class UserMother
{
    private const DEFAULT_NAME = 'Test User';
    private const DEFAULT_EMAIL = 'test@example.com';
    private const DEFAULT_PASSWORD = 'password123';

    public static function create(
        ?string $name = null,
        ?string $email = null,
        ?string $password = null
    ): User {
        return User::create(
            $name ?? self::DEFAULT_NAME,
            $email ?? self::DEFAULT_EMAIL,
            $password ?? self::DEFAULT_PASSWORD
        );
    }

    public static function withCompany(Company $company): User
    {
        $user = self::create();
        $user->assignToCompany($company);
        return $user;
    }

    public static function withAdminRole(): User
    {
        $user = self::create();
        $user->setRoles([UserRole::ADMIN]);
        return $user;
    }

    public static function withUserRole(): User
    {
        $user = self::create();
        $user->setRoles([UserRole::USER]);
        return $user;
    }

    public static function withAge(int $age): User
    {
        $user = self::create();
        $user->setAge($age);
        return $user;
    }

    public static function active(): User
    {
        $user = self::create();
        $user->setIsActive(true); // To Refactor
        return $user;
    }
}