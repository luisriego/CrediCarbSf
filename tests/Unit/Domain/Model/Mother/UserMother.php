<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Mother;

use App\Domain\Common\UserRole;
use App\Domain\Model\Company;
use App\Domain\Model\User;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Password;
use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\UserName;

final class UserMother
{
    private const DEFAULT_ID = '123e4567-e89b-12d3-a456-426614174000';
    private const DEFAULT_NAME = 'Test User';
    private const DEFAULT_EMAIL = 'test@example.com';
    private const DEFAULT_PASSWORD = 'Password@123';

    public static function create(
        ?string $id = null,
        ?string $name = null,
        ?string $email = null,
        ?string $password = null
    ): User {
        return User::create(
            UserId::fromString($id ?? self::DEFAULT_ID),
            UserName::fromString($name ?? self::DEFAULT_NAME),
            Email::fromString($email ?? self::DEFAULT_EMAIL),
            Password::fromString($password ?? self::DEFAULT_PASSWORD)
        );
    }

    public static function withCompany(Company $company): User
    {
        $user = self::create();
        $user->assignToCompany($company);
        return $user;
    }

    public static function withInvalidName(string $name): User
    {
        return self::create(
            name: $name
        );
    }

    public static function withPassword(string $password): User
    {
        return self::create(
            password: $password
        );
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