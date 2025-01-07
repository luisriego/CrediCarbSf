<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Model\User;

class UserFactory
{
    public static function create($id, $name, $email, $password, $age): User
    {
        return User::create(
            $id,
            $name,
            $email,
            $password,
            $age,
        );
    }
}
